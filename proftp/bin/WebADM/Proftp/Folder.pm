#!/usr/bin/perl
package WebADM::Proftp::Folder;

use strict;
require Exporter;
use File::Basename;
use WebADM::Folder;
use Data::Dumper;
use File::Basename;

our @ISA     = qw(Exporter);
our @EXPORT  = qw(init_ftp_folder add_folder);
our $VERSION = 1.00;

my $ftphome_uid = undef;
my $ftphome_gid = undef;

sub init_ftp_folder{
	$ftphome_uid = getpwnam($::config->{'ftphome'}->{'user'});
	$ftphome_gid = getgrnam($::config->{'ftphome'}->{'group'});
	if(!$ftphome_uid || !$ftphome_gid){
		if($::config->{'ftphome'}->{'path'}){
			if($::config->{'cmd'}->{'useradd'}){
				if($::config->{'ftphome'}->{'user'} && $::config->{'ftphome'}->{'group'} && $::config->{'ftphome'}->{'mode'}){
					qx($::config->{'cmd'}->{'useradd'} -d '$::config->{'ftphome'}->{'path'}' -M -s '/sbin/nologin' $::config->{'ftphome'}->{'user'});
					$ftphome_uid = int(getpwnam($::config->{'ftphome'}->{'user'}));
					chown $ftphome_uid, $::config->{'default'}->{'min_gid'}, $::config->{'ftphome'}->{'path'};
					chmod oct($::config->{'ftphome'}->{'mode'}), $::config->{'ftphome'}->{'path'};
				}
				else{
					$$_[0] = "ERROR: Ftphome user, group or mode not defined in xml config\n";
					$::logger->error("Ftphome user, group or mode not defined in xml config");
					return 1;
				}
			}
			else{
				$$_[0] = "ERROR: CMD: Useradd not found - Please check your xml config\n";
				$::logger->error("CMD: Useradd not found - Please check your xml config");
				return 1;
			}
		}
		else{
			$$_[0] = "ERROR: Ftphome not found - Please create folder manually\n";
			$::logger->error("Ftphome not found - Please create folder manually");
			return 1;
		}
	}
}

sub add_folder{
#0=resultval,1=new folder
	if($::config->{'ftphome'}->{'path'}){
		if(-d $::config->{'ftphome'}->{'path'}){
			my @folders = ();
			my %fs_folders = ();
			my %db_folders = ();
			my $query = "";
			if($_[1]){
				if(! -d $_[1]){
					eval{
						$::logger->error("INFO: Creating directory: '$_[1]'");
						mkdir($_[1]);
					};
					if($@){
						$$_[0] = "ERROR: Unable to create directory '$_[1]': $@";
						$::logger->error("ERROR: Unable to create directory '$_[1]': $@");
						return 1;
					}
					else{
						push(@folders,$_[1]);
					}
				}
				else{
					$$_[0] = "ERROR: Unable to create directory '$_[1]' because it exists: $@";
					$::logger->error("ERROR: Unable to create directory '$_[1]' because it exists: $@");
					return 1;
				}
			}
			else{
				get_folder_tree($::config->{'ftphome'}->{'path'},\@folders);
			}
			my @depths_base = split("/",$::config->{'ftphome'}->{'path'});
			my $depth_base = @depths_base - 1;
			foreach (@folders){
				my @depths_child = split("/",$_);
				my $depth_child = @depths_child;
				$depth_child -= $depth_base;
				$fs_folders{$_}{'exist'} = 1;
				$fs_folders{$_}{'depth'} = $depth_child;
				$fs_folders{$_}{'name'} = basename($_);
				if($depth_child == 1){
					$fs_folders{$_}{'parent_path'} = undef;
				}
				else{
					$fs_folders{$_}{'parent_path'} = dirname($_);
				}
			}
			if($_[1] && $fs_folders{$_[1]}{'parent_path'}){
				$query = "SELECT * FROM folders WHERE path = '".$fs_folders{$_[1]}{'parent_path'}."' ORDER BY path;";
			}
			else{
				$query = "SELECT * FROM folders ORDER BY path;";
			}
			my $sth = $::dbh->prepare($query);
			$sth->execute();
			while(my $ref = $sth->fetchrow_hashref()){
				$db_folders{$ref->{'path'}}{'exist'} = 1;
				$db_folders{$ref->{'path'}}{'name'} = $ref->{'name'};
				$db_folders{$ref->{'path'}}{'depth'} = $ref->{'depth'};
				$db_folders{$ref->{'path'}}{'parent_id'} = $ref->{'parent_id'};
			}
			$sth->finish();
			foreach my $path (sort keys %fs_folders){
				if(!$db_folders{$path}{'exist'}){
					my $parent_id = 0;
					if($fs_folders{$path}{'depth'} > 1){
						$sth->finish();
						$sth = $::dbh->prepare("SELECT id FROM folders WHERE path = '$fs_folders{$path}{'parent_path'}';");
						$sth->execute();
						my $ref = $sth->fetchrow_hashref();
						$parent_id = $ref->{'id'};
						$sth->finish();
					}
					my $query = "INSERT INTO folders (parent_id,path,name,depth,created_by,created) VALUES ('$parent_id','$path','$fs_folders{$path}{'name'}','$fs_folders{$path}{'depth'}','webadm',NOW());";
					$sth = $::dbh->prepare($query);
					$sth->execute();
					$sth->finish();
					eval{
						chown $ftphome_uid, $::config->{'default'}->{'min_gid'}, $path;
					};
					if($@){
						$$_[0] = "ERROR: Unable to change ownership of directory '$path': $@";
						$::logger->error("ERROR: Unable to change ownership of directory '$path': : $@");
						return 1;
					}
					eval{
						chmod oct($::config->{'ftphome'}->{'mode'}), $path;
					};
					if($@){
						$$_[0] = "ERROR: Unable to change permissions of directory '$path': $@";
						$::logger->error("ERROR: Unable to change permissions of directory '$path': : $@");
						return 1;
					}
				}
			}
		}
		else{
			$$_[0] = "ERROR: Ftphome not found - Please create folder manually";
			$::logger->error("Ftphome not found - Please create folder manually");
			return 1;
		}
	}
	else{
		$$_[0] = "ERROR: Ftphome not defined in config xml";
		$::logger->error("Ftphome not defined in config xml");
		return 1;
	}
	return 2;
}

1;
