#!/usr/bin/perl
#
# import_pg.pl
#
# import posts from the older version of Postgenomic
#

use lib (".");
use strict;
use DBI;
use config qw(%config log log_error urldecode $DEBUG parse_post_xml url_breakdown do_sleep);
use helper qw(get_pubmed_metadata get_oai_metadata get_crossref_metadata get_last_id search_pubmed_doi);
use XML::Simple;
use Digest::MD5 qw(md5_hex);
use Date::Parse;
use Encode qw(encode);

my $encoding = "ascii";

my $connection_string = sprintf("dbi:mysql:%s:%s", $config{"db_name"}, $config{"db_host"});
my $db = DBI->connect($connection_string, $config{"db_user"}, $config{"db_password"}) or log_error("Couldn't connect to the database.\n");

my $old_connection_string = sprintf("dbi:mysql:%s:%s", "basepairs", "localhost");
my $old_db = DBI->connect($old_connection_string, "root", "postgenomic01") or log_error("Couldn't connect to the database.\n");

my %tags;

my $sql = $old_db->prepare("SELECT * FROM post_tag WHERE !ISNULL(post_id)");
$sql->execute();

while (my $row = $sql->fetchrow_hashref()) {
	my $post_id = $row->{"post_id"};
	
	my @tags = ();
	if ($tags{$post_id}) {
		@tags = @{$tags{$post_id}};
	}

	push(@tags, $row->{"tag"});

	$tags{$post_id} = \@tags;
	print STDERR "t";
}

my $sql = $old_db->prepare("SELECT posts.*, feeds.feed_url FROM posts, feeds WHERE feeds.feed_id=posts.feed_id");
$sql->execute();

while (my $row = $sql->fetchrow_hashref()) {
	my $title = $row->{"title"};
	my $content = $row->{"content"};
	my $url = $row->{"url"};
	my $post_id = $row->{"post_id"};
	my $pubdate = $row->{"created"};
	my $feed_url = $row->{"feed_url"};
	
	my $feed_hash = md5_hex($feed_url);
	my $feed_dir = "posts/$feed_hash";
	if (-e $feed_dir) {
		my $filename = $feed_dir."/post_".md5_hex($url);
	
		if (-e $filename) {
			print STDERR "o";
		} else {
			print STDERR "O";

			my $xml = sprintf("<?xml version=\"1.0\" encoding=\"ascii\"?>\n<post>\n\t<feed_id>%s</feed_id>\n\t<title>%s</title>\n\t<link>%s</link>\n\t<date>%s</date>\n", $feed_hash, "<![CDATA[".$title."]]>", "<![CDATA[".$url."]]>", $pubdate);
			
			# add any tags
			if ($tags{$post_id}) {
				my @tags = @{$tags{$post_id}};
				foreach my $tag (@tags) {
					$xml .= sprintf("\t<tag>%s</tag>\n", "<![CDATA[".$tag."]]>");
				}
			}

			$xml .= sprintf("\t<description>%s</description>\n</post>", "<![CDATA[".$content."]]>");

			open(FILE, ">$filename");
			print FILE $xml;
			close(FILE);
		}
	} else {
		print STDERR ".";
	}
}

