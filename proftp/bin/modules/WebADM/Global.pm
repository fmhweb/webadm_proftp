package WebADM::Global;

require Exporter;
use Data::Dumper;
use File::Path qw(make_path remove_tree);

our @ISA     = qw(Exporter);
our @EXPORT  = qw(execCmd addDirectory getFileContent);
our $VERSION = 1.00;

sub execCmd{
	#0=command,1=params,2=result ref,3=reverse exitcode
        if($_[0]){
                my $cmd = "LANG=C ".$_[0];
		my $err = undef;
                if($_[1]){$cmd .= " $_[1]";}
                if($::param{'bool'}{'verbose'}){
			print "EXECCMD: >> $cmd <<\n";
	                if($_[2]){(@{$_[2]},$err) = qx($cmd);}
			else{my @result = qx($cmd);}
		}
		else{
	                if($_[2]){(@{$_[2]},$err) = qx($cmd 2>/dev/null);}
			else{my @result = qx($cmd >/dev/null 2>&1);}
		}
                if($? == 0){
			$::logger->info("EXECCMD: success $cmd");
			if($_[2] && @{$_[2]}){foreach (@{$_[2]}){chomp();}}
                        return 0;
                }
                else{$::logger->error("EXECCMD: failed $cmd");}
        }
        else{$::logger->error("command missing - Check the xml config file: '$::param{'path'}{'xml'}'");}
        return 1;
}

sub addDirectory{
        my ($dir,$mode,$user,$group) = @_;
        if($dir && ! -d $dir){
                $::logger->info("Creating directory: '$dir'");
                make_path("$dir", {
                        mode => $mode,
                });
        }
	if(-d $dir){
		if($mode){
			my $exitcode = execCmd("$::config->{'cmd'}->{'chmod'}","$mode '$dir'");
                        if($exitcode){
                                $::logger->error("ADDDIRECTORY: Unable to change mode of directory '$dir' - mode='$mode'");
                                return 1;
                        }
		}
		if($user && $group){
			my $exitcode = execCmd("$::config->{'cmd'}->{'chown'}","$user:$group '$dir'");
			if($exitcode){
				$::logger->error("ADDDIRECTORY: Unable to change ownership of directory '$dir' - user='$user',group='$group'");
				return 1;
			}
		}
		return 0;
	}
	else{$::logger->error("ADDDIRECTORY: Unable to create directory: '$dir'");}
        return 1;
}

sub getFileContent{
	my ($path,$refresult) = @_;
	if($path && -f $path ){
		eval{
			open FILE,"<$path" or die "Cannot open file '$path': $!";
			@$refresult = <FILE>;
			close(FILE);
		}
		or do{
			$::logger->error("READFILE: Cannot open file '$path': $@");
			return -1;
		};
	}
	return 0;
}
