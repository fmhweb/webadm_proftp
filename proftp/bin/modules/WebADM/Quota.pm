package WebADM::Quota;

use strict;
use warnings;
require Exporter;
use WebADM::Global;
use WebADM::Sql;
use Data::Dumper;

our @ISA     = qw(Exporter);
our @EXPORT  = qw(quotaSet quotaCheck);
our $VERSION = 1.00;

sub quotaSet{
	#USERADD: params: 0=username;1=surname;2=firstname;3=passwd;4=email;5=company;6=telephon;7=fax;8=address;9=comment;10=gid;11=homedir;12=shell;13=disabled;14=expires
	#                 15=soft quota bytes;16=hard quota bytes;17=soft inodes;18=hard inodes
	my ($type,$refparams,$guiuser) = @_;
	if($$refparams[0] && defined($$refparams[15]) && defined($$refparams[16]) && defined($$refparams[17]) && defined($$refparams[18]) && $::param{'default'}{'device'} && $::param{'default'}{'blocksize'}){
		my $userid = getSqlUserId($$refparams[0]);
		if($userid){
			$::logger->info("SETQUOTA: Setting quota for '$$refparams[0]': Soft bytes: '$$refparams[15]', Hard bytes: '$$refparams[16]', Soft inodes: '$$refparams[17]', Hard inodes: '$$refparams[18]'");
			my $softblocks = sprintf("%.0f",($$refparams[15] / $::param{'default'}{'blocksize'}));
			my $hardblocks = sprintf("%.0f",($$refparams[16] / $::param{'default'}{'blocksize'}));
			$::logger->info("SETQUOTA: Changing bytes to blocks (Blocksize: $::param{'default'}{'blocksize'}): Soft blocks: '$softblocks', Hard blocks: '$hardblocks', Soft inodes: '$$refparams[17]', Hard inodes: '$$refparams[18]'");
			my $exitcode = execCmd("$::config->{'cmd'}->{'setquota'}","$type $$refparams[0] $softblocks $hardblocks $$refparams[17] $$refparams[18] $::param{'default'}{'device'}");
			if(!$exitcode){return 0;}
			$::logger->error("SETQUOTA: Failed for user: '$$refparams[0]'");
		}
		else{$::logger->error("SETQUOTA: Unable to find user in DB: '$$refparams[0]'");}
	}
	return 1;
}

sub quotaCheck{
	if($::param{'default'}{'device'} && $::param{'default'}{'blocksize'}){
		my %types = ('user' => "-u",'group' => "-g");
		foreach my $type (keys %types){
			my %diskquotas = ();
			my %dbquotas = ();
			my @result = ();
			my $exitcode = execCmd("$::config->{'cmd'}->{'repquota'}","$types{$type} $::param{'default'}{'device'}",\@result);
			if(!$exitcode){
				if($result[1] =~ m/((\d+)days.+(\d+)days)/){
					my $blockgrace = $1;
					my $inodegrace = $2;
					my $i = 5;
					while($result[$i]){
						if(!($result[$i] =~ m/^\#/)){
							$result[$i] =~ s/\s+/ /g;
							my @values = split(" ",$result[$i]);
							my $sub = 0;
							my $blockgrace = "";
							my $inodegrace = "";
							if($values[5] =~ m/(\d+)days/){$blockgrace = $1;}
							else{$sub = 1;}
							if($values[9] && $values[9] =~ m/(\d+)days/){$inodegrace = $1;}
							$diskquotas{$values[0]}{'flags'} = $values[1];
							$diskquotas{$values[0]}{'blocksused'} = $values[2];
							$diskquotas{$values[0]}{'bytesused'} = $values[2] * $::param{'default'}{'blocksize'};
							$diskquotas{$values[0]}{'blockssoft'} = $values[3];
							$diskquotas{$values[0]}{'bytessoft'} = $values[3] * $::param{'default'}{'blocksize'};
							$diskquotas{$values[0]}{'blockshard'} = $values[4];
							$diskquotas{$values[0]}{'byteshard'} = $values[4] * $::param{'default'}{'blocksize'};
							$diskquotas{$values[0]}{'blocksgrace'} = $blockgrace;
							$diskquotas{$values[0]}{'inodesused'} = $values[(6 - $sub)];
							$diskquotas{$values[0]}{'inodessoft'} = $values[(7 - $sub)];
							$diskquotas{$values[0]}{'inodeshard'} = $values[(8 - $sub)];
							$diskquotas{$values[0]}{'inodesgrace'} = $inodegrace;
						}
						$i++;
					}
				}
				my $sth = $::dbh->prepare("SELECT a.*,b.$type"."name FROM quotas_$type a LEFT JOIN $type"."s b ON a.$type"."id = b.$type"."id;");
				$sth->execute();
				while(my $result = $sth->fetchrow_hashref()){
					$dbquotas{$result->{'username'}}{'flags'} = $result->{'flags'};
					$dbquotas{$result->{'username'}}{'blocksused'} = $result->{'blocksused'};
					$dbquotas{$result->{'username'}}{'bytesused'} = $result->{'bytesused'};
					$dbquotas{$result->{'username'}}{'blockssoft'} = $result->{'blockssoft'};
					$dbquotas{$result->{'username'}}{'bytessoft'} = $result->{'bytessoft'};
					$dbquotas{$result->{'username'}}{'blockshard'} = $result->{'blockshard'};
					$dbquotas{$result->{'username'}}{'byteshard'} = $result->{'byteshard'};
					$dbquotas{$result->{'username'}}{'blocksgrace'} = $result->{'blocksgrace'};
					$dbquotas{$result->{'username'}}{'inodesused'} = $result->{'inodesused'};
					$dbquotas{$result->{'username'}}{'inodessoft'} = $result->{'inodessoft'};
					$dbquotas{$result->{'username'}}{'inodeshard'} = $result->{'inodeshard'};
					$dbquotas{$result->{'username'}}{'inodesgrace'} = $result->{'inodesgrace'};
				}
				foreach my $username (keys %diskquotas){
					my $userid = getSqlUserId($username);
					if($userid){
						if(!$dbquotas{$username}){
							my $exitcode = execSql("INSERT INTO quotas_$type (userid,flags,blocksused,bytesused,blockssoft,bytessoft,blockshard,byteshard,blocksgrace,inodesused,inodessoft,inodeshard,inodesgrace,last_update) VALUES ('$userid','$diskquotas{$username}{'flags'}','$diskquotas{$username}{'blocksused'}','$diskquotas{$username}{'bytesused'}','$diskquotas{$username}{'blockssoft'}','$diskquotas{$username}{'bytessoft'}','$diskquotas{$username}{'blockshard'}','$diskquotas{$username}{'byteshard'}','$diskquotas{$username}{'blocksgrace'}','$diskquotas{$username}{'inodesused'}','$diskquotas{$username}{'inodessoft'}','$diskquotas{$username}{'inodeshard'}','$diskquotas{$username}{'inodesgrace'}',NOW());");
						}
						else{
							my $query = "";
							foreach my $key (keys $diskquotas{$username}){
								if($diskquotas{$username}{$key} ne $dbquotas{$username}{$key}){
									$query .= "$key = '$diskquotas{$username}{$key}', ";
								}
							}
							if($query){
								$query = "UPDATE quotas_$type SET $query last_update = NOW() WHERE userid = '$userid'";
								my $exitcode = execSql($query);
							}
						}
					}
					else{$::logger->error("CHECKQUOTA: Unable to find user in db: '$username'");}
				}
			}
			else{$::logger->error("CHECKQUOTA: Failed - Unable to execute repquota: '$::config->{'cmd'}->{'repquota'}'");}
		}
	}
	return 1;
}

#$VAR1 = '*** Report for user quotas on device /dev/sdb1';
#$VAR2 = 'Block grace time: 7days; Inode grace time: 7days';
#$VAR3 = '                        Block limits                File limits';
#$VAR4 = 'User            used    soft    hard  grace    used  soft  hard  grace';
#$VAR5 = '----------------------------------------------------------------------';
#$VAR6 = 'root      --      13       0       0              2     0     0       ';
#$VAR7 = 'ftp       --       2       0       0              1     0     0       ';
#$VAR8 = '#10002    +-       7       6       7  6days       4     0     0       ';
#$VAR9 = '';
#$VAR10 = '';
