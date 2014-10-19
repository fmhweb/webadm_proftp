package WebADM::Auth;



require Exporter;

our @ISA     = qw(Exporter);
our @EXPORT  = qw(checkme);   # symbols to be exported by default (space-separated)
our $VERSION = 1.00;                  # version number

sub checkme{
	print "HELLO ".$::config->{'cmd'}->{'useradd'}."\n";
}
