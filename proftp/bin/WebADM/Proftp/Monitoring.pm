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
	my $xfer_rrd = $::paths{'rrd'}."/xfer.rrd";
	my $rrd = RRD::Simple->new(file => $xfer_rrd);
	if(-f $xfer_rrd){
		$timebegin = $rrd->last();
	}
	else{
		$rrd->create(
			bytesIn => "GAUGE",
			bytesOut => "GAUGE",
			bytesXfer => "GAUGE"
		);
		$timebegin = time;
	}
	$timebegin -= $timebegin % $timesteps;
	$timebegin += $timesteps;
	$timeend -= $timeend % $timesteps;
	if($timebegin < ($timeend - $timesteps * 12)){$timebegin = $timeend - $timesteps * 12;}#Später von 1er stunde auf 1 woche erhoehen
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
	$query = "SELECT userid,command, SUM(size) AS size, DATE(time) AS date, SEC_TO_TIME(TIME_TO_SEC(time) - TIME_TO_SEC(time)%($timesteps)) AS intervals FROM xfer WHERE errorlevel = 'info' AND time > '$datebegin' AND time < '$dateend' GROUP BY intervals,command ORDER BY date,intervals;";
	print "$datebegin\n$dateend\n$query\n";
	$sth = $::dbh->prepare($query);
	$sth->execute();
	my %results = ();
	my $starttime = undef;
	while(my $ref = $sth->fetchrow_hashref()){
		my $timepiece = Time::Piece->strptime($ref->{'date'}." ".$ref->{'intervals'},"%Y-%m-%d %H:%M:%S");
		$timepiece -= $timepiece->localtime->tzoffset;
		if(!$starttime){$starttime = $timepiece->epoch;}
		$results{$timepiece->epoch}{$ref->{'userid'}}{$ref->{'command'}} = $ref->{'size'};
		$results{$timepiece->epoch}{$ref->{'userid'}}{'date'} = $ref->{'date'}." ".$ref->{'intervals'};
		$results{$timepiece->epoch}{$ref->{'userid'}}{'time'} = $timepiece->epoch;;
	}
	$sth->finish();
	foreach my $userid (@userids){
		for(my $timestamp = $timebegin;$timestamp <= $timeend; $timestamp += $timesteps){
			if(!$results{$timestamp}{$userid}{'STOR'}){$results{$timestamp}{$userid}{'STOR'} = 0;}
			if(!$results{$timestamp}{$userid}{'RETR'}){$results{$timestamp}{$userid}{'RETR'} = 0;}
		}
	}
	foreach my $resulttime (sort keys %results){
		my $resultdate = localtime($resulttime)->strftime("%Y-%m-%d %H:%M:%S");
		print "$resulttime - $resultdate\n";
		my $count_stor = 0;
		my $count_retr = 0;
		foreach my $userid (@userids){
			my $xfer_user_rrd = $::paths{'rrd'}."/xfer_$userid.rrd";
			my $rrd_user = RRD::Simple->new(file => $xfer_user_rrd);
			if(! -f $xfer_user_rrd){
				$rrd_user->create(
					bytesIn => "GAUGE",
					bytesOut => "GAUGE",
					bytesXfer => "GAUGE"
				);
			}
			$rrd_user->update($xfer_user_rrd, $resulttime,
				bytesIn => $results{$resulttime}{$userid}{'STOR'},
				bytesOut => $results{$resulttime}{$userid}{'RETR'},
				bytesXfer => ($results{$resulttime}{$userid}{'STOR'} + $results{$resulttime}{$userid}{'RETR'})
			);
			print $userid." - ".$results{$resulttime}{$userid}{'STOR'}."\n";
			print $userid." - ".$results{$resulttime}{$userid}{'RETR'}."\n";
			$count_stor += $results{$resulttime}{$userid}{'STOR'};
			$count_retr += $results{$resulttime}{$userid}{'RETR'};
		}
		$rrd->update($xfer_rrd, $resulttime,
			bytesIn => $count_stor,
			bytesOut => $count_retr,
			bytesXfer => ($count_stor + $count_retr)
		);
		print "$count_stor\n$count_retr\n";
	}
	return 2;
}

1;

#Transfer in
#Transfer out
