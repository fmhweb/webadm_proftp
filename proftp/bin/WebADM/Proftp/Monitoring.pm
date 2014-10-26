#!/usr/bin/perl
package WebADM::Proftp::Monitoring;

use strict;
require Exporter;
use File::Basename;
use WebADM::Folder;
use Data::Dumper;
use File::Basename;
use POSIX qw(strftime);
use RRD::Simple;
use Time::Piece;

our @ISA     = qw(Exporter);
our @EXPORT  = qw(create_xfer_rrd);
our $VERSION = 1.00;

my $ftphome_uid = undef;
my $ftphome_gid = undef;

sub create_xfer_rrd{
	my $timesteps = 300;
	my $timebegin = undef;
	my $timeend = time;
	my $xfer_rrd = $::paths{'rrd'}."/xfer_all.rrd";
	my $rrd = RRD::Simple->new(file => $xfer_rrd);
	if(-f $xfer_rrd){$timebegin = $rrd->last();}
	else{$timebegin = $timeend - ($timesteps * 12 * 24 * 7);}
	$timebegin -= $timebegin % $timesteps;
	$timeend -= $timeend % $timesteps;
	$timebegin += $timesteps;
	if($timebegin != $timeend){
		if(! -f $xfer_rrd){
			$rrd->create(
				bytesIn => "GAUGE",
				bytesOut => "GAUGE",
				bytesXfer => "GAUGE",
				filesIn => "GAUGE",
				filesOut => "GAUGE",
				filesXfer => "GAUGE"
			);
		}
		my $datebegin = localtime($timebegin)->strftime("%Y-%m-%d %H:%M:%S");
		my $dateend = localtime($timeend)->strftime("%Y-%m-%d %H:%M:%S");
		my $query = "SELECT userid FROM users;";
		my $sth = $::dbh->prepare($query);
		$sth->execute();
		my @userids = ();
		while(my $ref = $sth->fetchrow_hashref()){
			push(@userids,$ref->{'userid'});
		}
		$sth->finish();
		$query = "SELECT userid,command, COUNT(command) as count, SUM(size) AS size, DATE(time) AS date, SEC_TO_TIME(TIME_TO_SEC(time) - TIME_TO_SEC(time)%($timesteps)) AS intervals FROM xfer WHERE errorlevel = 'info' AND time > '$datebegin' AND time < '$dateend' GROUP BY userid,intervals,command ORDER BY date,intervals;";
		print "Begin: $datebegin\nEnd: $dateend\n$query\n" if $::opt_verbose;
		$sth = $::dbh->prepare($query);
		$sth->execute();
		my %results = ();
		my $starttime = undef;
		while(my $ref = $sth->fetchrow_hashref()){
			my $timepiece = Time::Piece->strptime($ref->{'date'}." ".$ref->{'intervals'},"%Y-%m-%d %H:%M:%S");
			$timepiece -= $timepiece->localtime->tzoffset;
			if(!$starttime){$starttime = $timepiece->epoch;}
			$results{$timepiece->epoch}{$ref->{'userid'}}{$ref->{'command'}}{'count'} = $ref->{'count'};
			$results{$timepiece->epoch}{$ref->{'userid'}}{$ref->{'command'}}{'size'} = $ref->{'size'};
			$results{$timepiece->epoch}{$ref->{'userid'}}{'date'} = $ref->{'date'}." ".$ref->{'intervals'};
			$results{$timepiece->epoch}{$ref->{'userid'}}{'time'} = $timepiece->epoch;;
		}
		$sth->finish();
		foreach my $userid (@userids){
			for(my $timestamp = $timebegin;$timestamp < $timeend; $timestamp += $timesteps){
				if(!$results{$timestamp}{$userid}{'STOR'}{'size'}){$results{$timestamp}{$userid}{'STOR'}{'size'} = 0;}
				if(!$results{$timestamp}{$userid}{'STOR'}{'count'}){$results{$timestamp}{$userid}{'STOR'}{'count'} = 0;}
				if(!$results{$timestamp}{$userid}{'RETR'}{'size'}){$results{$timestamp}{$userid}{'RETR'}{'size'} = 0;}
				if(!$results{$timestamp}{$userid}{'RETR'}{'count'}){$results{$timestamp}{$userid}{'RETR'}{'count'} = 0;}
			}
		}
		foreach my $resulttime (sort keys %results){
			my $resultdate = localtime($resulttime)->strftime("%Y-%m-%d %H:%M:%S");
			print "$resulttime - $resultdate\n" if $::opt_verbose;
			my $count_stor_size = 0;
			my $count_stor_count = 0;
			my $count_retr_size = 0;
			my $count_retr_count = 0;
			foreach my $userid (@userids){
				my $xfer_user_rrd = $::paths{'rrd'}."/xfer_$userid.rrd";
				my $rrd_user = RRD::Simple->new(file => $xfer_user_rrd);
				if(! -f $xfer_user_rrd){
					$rrd_user->create(
						bytesIn => "GAUGE",
						bytesOut => "GAUGE",
						bytesXfer => "GAUGE",
						filesIn => "GAUGE",
						filesOut => "GAUGE",
						filesXfer => "GAUGE"
					);
				}
				$rrd_user->update($xfer_user_rrd, $resulttime,
					bytesIn => $results{$resulttime}{$userid}{'STOR'}{'size'},
					bytesOut => $results{$resulttime}{$userid}{'RETR'}{'size'},
					bytesXfer => ($results{$resulttime}{$userid}{'STOR'}{'size'} + $results{$resulttime}{$userid}{'RETR'}{'size'}),
					filesIn => $results{$resulttime}{$userid}{'STOR'}{'count'},
					filesOut => $results{$resulttime}{$userid}{'RETR'}{'count'},
					filesXfer => ($results{$resulttime}{$userid}{'STOR'}{'count'} + $results{$resulttime}{$userid}{'RETR'}{'count'})
				);
				print $userid." - Bytes in: ".$results{$resulttime}{$userid}{'STOR'}{'size'}."\n" if $::opt_verbose;
				print $userid." - Files in: ".$results{$resulttime}{$userid}{'STOR'}{'count'}."\n" if $::opt_verbose;
				print $userid." - Bytes out: ".$results{$resulttime}{$userid}{'RETR'}{'size'}."\n" if $::opt_verbose;
				print $userid." - Files out: ".$results{$resulttime}{$userid}{'RETR'}{'count'}."\n" if $::opt_verbose;
				$count_stor_size += $results{$resulttime}{$userid}{'STOR'}{'size'};
				$count_stor_count += $results{$resulttime}{$userid}{'STOR'}{'count'};
				$count_retr_size += $results{$resulttime}{$userid}{'RETR'}{'size'};
				$count_retr_count += $results{$resulttime}{$userid}{'RETR'}{'count'};
			}
			$rrd->update($xfer_rrd, $resulttime,
				bytesIn => $count_stor_size,
				bytesOut => $count_retr_size,
				bytesXfer => ($count_stor_size + $count_retr_size),
				filesIn => $count_stor_count,
				filesOut => $count_retr_count,
				filesXfer => ($count_stor_count + $count_retr_count)
			);
			print "Bytes in: $count_stor_size\n" if $::opt_verbose;
			print "Files in: $count_stor_count\n" if $::opt_verbose;
			print "Bytes out: $count_retr_size\n" if $::opt_verbose;
			print "Files out: $count_retr_count\n" if $::opt_verbose;
		}
	}
	return 2;
}

1;

#Transfer in
#Transfer out
