#!/usr/bin/perl
package WebADM::Proftp::Monitoring;

use strict;
require Exporter;
use File::Basename;
use WebADM::Folder;
use Data::Dumper;
use File::Basename;
use POSIX qw(strftime);

our @ISA     = qw(Exporter);
our @EXPORT  = qw(create_charts);
our $VERSION = 1.00;

my $ftphome_uid = undef;
my $ftphome_gid = undef;

sub create_charts{
	my $date = strftime "%Y-%m-%d %T", localtime(time - $::time{'chart'}{'taken'});
	my $query = "SELECT command,size FROM xfer WHERE errorlevel = 'info' && time >= '$date';";
	print "$query\n";
	my $sth = $::dbh->prepare($query);
	$sth->execute();
	while(my $ref = $sth->fetchrow_hashref()){
		print $ref->{'command'}." - ".$ref->{'size'}."\n";
	}
	$sth->finish();
	return 2;
}

1;
