#!/usr/bin/perl
package WebADM::Folder;

use strict;
require Exporter;
use File::Path qw(make_path remove_tree);
use File::Basename;
use Data::Dumper;

our @ISA     = qw(Exporter);
our @EXPORT  = qw(create_folder check_file_dir get_folder_tree);
our $VERSION = 1.00;

sub create_folder{
#0=path,1=mode,2=user,3=group
	if($_[0]){
		if(! -d $_[0]){
			make_path("$_[0]");
			if($::logger){
				if(-d $_[0]){$::logger->info("CREATEFOLDER: Creating folder '$_[0]': Successfull");}
				else{$::logger->error("CREATEFOLDER: Creating folder '$_[0]': Failed");}
			}
		}
		if(-d $_[0]){
			if($_[1] && $_[1] =~ m/^\d{4,4}$/){
				$::logger->info("CREATEFOLDER: Changing permissions of '$_[0]' to: '$_[1]'");
				chmod $_[1], $_[0];
			}
			if($_[2] && $_[3]){
				$::logger->info("CREATEFOLDER: Changing ownership of '$_[0]' to: '$_[2]:$_[3]'");
				if(! $_[2] =~ m/^\d+$/){$_[2] = getpwnam($_[2]);}
				if(! $_[3] =~ m/^\d+$/){$_[3] = getpwnam($_[3]);}
				chown $_[2], $_[3], $_[0];
			}
		}
	}
	else{$::logger->warn("CREATEFOLDER: Error in function create_folder - Parameter missing: path='$_[0]'");}
}

sub check_file_dir{
#0=path,1=mode,2=user,3=group
	if($_[0]){
		my $dirname = dirname($_[0]);
		if($::logger){$::logger->info("CHECKFILEPATH: Check if dirname of file '$_[0]' exists: '$dirname'");}
		if(!$_[1]){$_[1] = "";}
		if(!$_[2]){$_[2] = "";}
		if(!$_[3]){$_[3] = "";}
		create_folder($dirname,$_[1],$_[2],$_[3]);
	}
	else{$::logger->warn("CHECKFILEPATH: Error in function check_file_path - Parameter missing: path='$_[0]'");}
}

sub get_folder_tree{
#0=path,1=arrayref
	if($_[0] && $_[1]){
		if(-d $_[0]){
			push(@{$_[1]},$_[0]);
			my @content = <$_[0]/*>;
			foreach (@content){
				if(-d){
					get_folder_tree($_,$_[1]);
				}
			}
		}
	}
	else{$::logger->warn("GETFOLDERTREE: Error in function get_folder_tree - Parameter missing: path='$_[0]'");}
}

1;
