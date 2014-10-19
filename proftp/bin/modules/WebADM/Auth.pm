package WebADM::Auth;

require Exporter;
use WebADM::Global;
use WebADM::Sql;

our @ISA     = qw(Exporter);
our @EXPORT  = qw(userAddMod userGetNextId groupAdd getId groupGetNextId);   # symbols to be exported by default (space-separated)
our $VERSION = 1.00;                  # version number

### ======== User ========

sub userAddMod{
	#USERADD: params: 0=username;1=surname;2=firstname;3=passwd;4=email;5=company;6=telephon;7=fax;8=address;9=comment;10=gid;11=homedir;12=shell;13=disabled;14=expires
	#                 15=soft quota bytes;16=hard quota bytes;17=soft inodes;18=hard inodes
        my ($refparams,$guiusername) = @_;
	if($$refparams[0] && $$refparams[10] && $$refparams[11] && $$refparams[12] && defined($$refparams[13]) && $$refparams[14]){
		my $exitcode = 0;
                my $exist = getId('-u',$$refparams[0]);
		my $comment = buildComment($$refparams[1],$$refparams[2],$$refparams[4],$$refparams[5],$guiusername);
                if(!$exist){
			$exitcode = -1;
                        $::logger->info("USERADD: Name: $$refparams[0] - Homedir: $$refparams[11] - Comment: $comment");
                        my $uid = userGetNextId();
                        if($uid >= $::param{'default'}{'min'}{'uid'}){
                                if(!$$refparams[3]){$$refparams[3] = randPasswd();}
                                if(! -d $$refparams[11]){addDirectory($$refparams[11],$::config->{'default'}->{'dir'}->{'mode'},$::config->{'default'}->{'dir'}->{'user'},$::config->{'default'}->{'dir'}->{'group'});}
                                $exitcode = execCmd("$::config->{'cmd'}->{'useradd'}","-M -u ".$uid." -g $$refparams[10] $$refparams[0]");
                                if(!$exitcode){
                                        $exitval = getId('-u',$$param[0]);
                                        if($exitval){
						$exitcode = execSql("INSERT INTO users (username,surname,firstname,passwd,email,company,telephon,fax,address,comment,uid,gid,homedir,shell,created_by,created,disabled,expires) VALUES ('$$refparams[0]','$$refparams[1]','$$refparams[2]','$$refparams[3]','$$refparams[4]','$$refparams[5]','$$refparams[6]','$$refparams[7]','$$refparams[8]','$$refparams[9]','$uid','$$refparams[10]','$$refparams[11]','$$refparams[12]','$guiusername',NOW(),'$$refparams[13]','$$refparams[14]');");
					}
					else{
						$::logger->error("USERADD: Unable to add user - User not found in '$::config->{'path'}->{'passwd'}'");
						return 1;
					}
				}
                                else{
					$::logger->error("USERADD: Unable to add user - Command failed");
					return 1;
                                }
                        }
                        else{
				$::logger->error("USERADD: Unable to add user - An error occured collecting the next user id - Min: '$uid' >= '$::param{'default'}{'min'}{'uid'}' failed");
				return 1;
			}
                }
		if(!$exitcode){
                        $::logger->info("USERMOD: Name: $$refparams[0] - Homedir: $$refparams[11] - Comment: $comment");
			if($$refparams[14] eq "0000-00-00"){$$refparams[14] = "";}
			my $exitcode = execCmd("$::config->{'cmd'}->{'usermod'}","-d '$$refparams[11]' -c '$comment' -s '$$refparams[12]' -e '$$refparams[14]' $$refparams[0]");
			if(!$exitcode){
				eval{
					open my $pipe, "|$::config->{'cmd'}->{'chpasswd'}" or die "can't open pipe '$::config->{'cmd'}->{'usermod'}': $!";
					print {$pipe} "$$refparams[0]:$$refparams[3]";
					close $pipe
				}
				or do{
					$::logger->error("USERMOD: Unable to set password for user");
					return 1;
				};
				$exitcode = execSql("UPDATE users SET username = '$$refparams[0]', surname = '$$refparams[1]', firstname = '$$refparams[2]', passwd = '$$refparams[3]', email = '$$refparams[4]', company = '$$refparams[5]', telephon = '$$refparams[6]',fax = '$$refparams[7]', address = '$$refparams[8]', comment = '$$refparams[9]',homedir = '$$refparams[11]', shell = '$$refparams[12]', changed_by = '$guiusername', changed = NOW(), disabled = '$$refparams[13]', expires = '$$refparams[14]';");
				return $exitcode;
			}
			else{
				$::logger->error("USERMOD: Unable to modify user - Command failed");
				return 1;
			}
		}
		
        }
        else{$::logger->error("USERADD: Unable to add user - Username '$$refparams[0]' or homedir '$$refparams[11]' missing");}
        return 1;
}

sub userGetNextId{
        if($::config->{'path'}->{'passwd'} && -f $::config->{'path'}->{'passwd'}){
                if($::config->{'default'}->{'min'}->{'uid'} =~ m/^\d+$/ && $::config->{'default'}->{'min'}->{'uid'} >= $::param{'default'}{'min'}{'uid'}){
                        my $nextid = $::config->{'default'}->{'min'}->{'uid'} - 1;
                        my @content = ();
			my $exitcode = getFileContent($::config->{'path'}->{'passwd'},\@content);
			if($exitcode != -1){
	                        foreach (@content){
                                	my @elements = split(":",$_);
                        	        if($elements[2] > $nextid){$nextid = $elements[2];}
                	        }
        	                $nextid++;
	                        return $nextid;
			}
			else{return 1;}
                }
                else{$::logger->error("USERGETNEXTID: Minuid '$::config->{'default'}->{'min'}->{'uid'}' missing or below '$::param{'default'}{'min'}{'uid'}' - Check the xml config file: '$::param{'path'}{'xml'}'");}
        }
        return 1;
}

### ======== Group ========

sub groupAdd{
        my ($groupname,$refgid) = @_;
        if($groupname){
                my $exitcode = getId('-g',$groupname,1);
                if($exitcode){
                        $::logger->info("GROUPADD: Name: $groupname");
                        $$refgid = groupGetNextId();
                        if($$refgid >= $::param{'default'}{'min'}{'gid'}){
                                $exitcode = execCmd("$::config->{'cmd'}->{'groupadd'}","-g ".$$refgid." $groupname");
                                if(!$exitcode){
                                        $exitcode = getId('-g',$groupname);
                                        if(!$exitcode){$::logger->error("GROUPADD: Unable to add group - User not found in '$::config->{'path'}->{'passwd'}'");}
                                        else{return 0;}
				}
                                else{$::logger->error("GROUPADD: Unable to add group - Command failed");}
                        }
                        else{$::logger->error("GROUPADD: Unable to add group - An error occured collecting the next group id - Min: '".$$refgid."' >= '$::param{'default'}{'min'}{'gid'}' failed");}
                }
                else{$::logger->error("GROUPADD: Unable to add group - Group '$groupname' already exists");}
        }
        else{$::logger->error("GROUPADD: Unable to add group - Groupname '$groupname' missing");}
        return 1;
}

sub groupGetNextId{
        if($::config->{'path'}->{'group'} && -f $::config->{'path'}->{'group'}){
                if($::config->{'default'}->{'min'}->{'gid'} =~ m/^\d+$/ && $::config->{'default'}->{'min'}->{'gid'} >= $::param{'default'}{'min'}{'gid'}){
                        my $nextid = $::config->{'default'}->{'min'}->{'gid'} - 1;
			my $exitcode = getFileContent($::config->{'path'}->{'group'},\@content);
                        if($exitcode != -1){
				foreach (@content){
                                	my @elements = split(":",$_);
					if($elements[2] > $nextid){$nextid = $elements[2];}
                	       }
        	               $nextid++;
	                       return $nextid;
			}
			else{return 1;}
                }
                else{$::logger->error("GROUPGETNEXTID: Mingid '$::config->{'default'}->{'min'}->{'gid'}' missing or below '$::param{'default'}{'min'}{'gid'}' - Check the xml config file: '$::param{'path'}{'xml'}'");}
        }
        else{$::logger->error("GROUPGETNEXTID: Group file is missing '$::config->{'path'}->{'group'}' - Check the xml config file: '$::param{'path'}{'xml'}'");}
        return 1;
}


sub randPasswd{
	my $length = 12;
	if($::config->{'default'}->{'passwdlength'}){$lenght = $::config->{'default'}->{'passwdlength'};}
        return qx(date +%s | sha256sum | base64 | head -c $length);
}

sub getId{
        if($_[0] && $_[1]){
		my $exitcode = 1;
		my @result = ();
                if($_[0] =~ m/^\d+$/){$exitcode = execCmd("$::config->{'cmd'}->{'id'}","$_[0] '$_[1]'",\@result);}
                else{$exitcode = execCmd("$::config->{'cmd'}->{'id'}","$_[0] -n '$_[1]'",\@result);}
		if($result[0]){return $result[0];}
        }
	return undef;
}

sub buildComment{
	my $comment = "";
	if($_[0]){$comment .= $_[0];}
	if($_[1]){if($comment){$comment .= ", ";};$comment .= $_[1];}
	if($_[2]){if($comment){$comment .= ", ";};$comment .= $_[2];}
	if($_[3]){if($comment){$comment .= ", ";};$comment .= $_[3];}
	if($comment){$comment .= " - ";}
	$comment .= "Created by $_[4] (".time.")";
	return $comment;
}

