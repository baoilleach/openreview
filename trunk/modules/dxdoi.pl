#!/usr/bin/perl
#
# dxdoi.pl
# 
# DOI resolver module for Postgenomic. Takes in an URL, returns a unique id (a DOI, in this case)
# 
# UNDERSTANDS: dx.doi.org/(.+)
#

use strict;
use lib ("..");

use helper;

my $url = $ARGV[0];

if ($url =~ /dx\.doi\.org\/(.+)/i) {
	print "DOI"."\t".$1."\n";
}
