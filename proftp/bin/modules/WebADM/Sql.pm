package WebADM::Sql;

require Exporter;
use Data::Dumper;

our @ISA     = qw(Exporter);
our @EXPORT  = qw(execSql getSqlUserId);
our $VERSION = 1.00;

sub execSql{
        my $sth = $::dbh->prepare($_[0]);
        $sth->execute();
        if($::dbh->err){$logger->error("MYSQL: Query failed: $_[0]");return 1;}
        if($::param{'log'}{'level'} > 2){$::logger->info("MYSQL: Query successfull: $_[0]");}
        return 0;
}

sub getSqlUserId{
        my $sth = $::dbh->prepare("SELECT userid FROM users WHERE username = '$_[0]';");
        $sth->execute();
        my $result = $sth->fetchrow_hashref();
        if($result->{'userid'}){return $result->{'userid'};}
        return 0;
}
