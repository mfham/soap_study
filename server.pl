use strict;
use warnings;
use SOAP::Transport::HTTP;

my $port = 8080;
my $deamon = SOAP::Transport::HTTP::Daemon
    ->new(LocalPort => $port)
    ->dispatch_to('name')
    ->handle();

sub name {
    my ($class, $name) = @_;
    return "Hello, $name !";
}
