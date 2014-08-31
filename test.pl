use SOAP::Lite;
print SOAP::Lite->proxy(shift)->name(shift)->result, "\n";


# perl server.pl 起動させておく
# perl test.pl http://url:8080 hoge
# => Hello, hoge !
