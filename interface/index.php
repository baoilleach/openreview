<? include("functions.php"); ?>
<?
	$PAGE_CACHE = 1;
	$PAGE_TYPE = "index";
?>
<? include("header.php"); ?>
<?
	$last_week = date("Y-m-d", mktime(0,0,0, date(m), date(d)-7,date(Y))); 
	$last_fortnight = date("Y-m-d", mktime(0,0,0, date(m), date(d)-14,date(Y))); 
	$last_month = date("Y-m-d", mktime(0,0,0, date(m)-1, date(d),date(Y))); 
		
	#$blogs = false;
	#if ($safe_category) {$blogs = get_blogs_with_tag($safe_category);}
	#$tags = get_tags_for_blogs($blogs);
	
	# take a random selection of tags
	#$random_tags = array();
	#$tag_keys = array_keys($tags);
	#for ($i=0; $i < 30; $i++) {
	#	$num = rand(0, sizeof($tags) - 1);
	#	$tag = $tag_keys[$num];
	#	$random_tags[$tag] = $tags[$tag];
	#}	
?>
<div class='content fullwidth'>
<div class='frontpage_welcome'>
<h1>Welcome to Postgenomic</h1>
<p>Postgenomic collects data from hundreds of science blogs and then does useful and interesting things with it.
<p>With Postgenomic, you can:
<ul>
<li>Find, read and subscribe to new <a href='<? plinkto("blogs.php", $page_vars); ?>'>science blogs</a>
<li>Find out what scientists are saying about <a href='<? plinkto("papers.php", $page_vars); ?>'>the latest papers</a>
<li>Read <a href='<? plinkto("posts.php", $page_vars, array("tag" => "review")); ?>'>mini-reviews</a>, <a href='<? plinkto("posts.php", $page_vars, array("tag" => "conference")); ?>'>conference reports</a> or even <a href='<? plinkto("posts.php", $page_vars, array("tag" => "original_research")); ?>'>original research</a>	
<li>See the buzz surrounding <a href='<? plinkto("links.php", $page_vars); ?>'>different websites</a>
<li>Browse different subject areas - <a href='<? plinkto("index.php", $page_vars, array("category" => "Chemistry")); ?>'>chemistry</a>, <a href='<? plinkto("index.php", $page_vars, array("category" => "Bioinformatics")); ?>'>bioinformatics</a>, <a href='<? plinkto("index.php", $page_vars, array("category" => "Neuroscience")); ?>'>neuroscience</a>, the <a href='<? plinkto("index.php", $page_vars, array("category" => "Life%20Sciences")); ?>'>life sciences</a>... see the "Explore" options on the top right hand side of the page for more.
</ul>
</div>
<div class='frontpage_tabs'>
<?
	print "<div class='frontpage_tab'>";
	print "<div class='tab_padding'>";
	print "<div class='tab_title'>Top Terms Today</div>";
	$terms = get_terms(32, $safe_category);
	$terms = clean_terms($terms);
	print_termcloud($terms);
	print "</div>";
	print "</div>";
	
	print "<div class='frontpage_tab' style='text-align: center; vertical-align: center;'>";
	print "<div class='tab_padding'>";
	#print "<h3>Top cited journals</h3>";
	print "<div class='tab_title'>Latest posts</div>";
	print "<div class='slidebox'>";
	$slides = print_blogger_slides($safe_category);
	print "</div>";
	#$journal_stats = get_journal_stats($safe_category);
	#print_journalcloud($journal_stats, 15);
	print "</div>";
	print "</div>";

	print "<div class='frontpage_tab'>";
	print "<div class='tab_padding'>";	
	print "<div class='tab_title'>Most active blogs</div>";
	$active_blogs = get_active_blogs($safe_category, $last_week, 15);
	print_blogcloud($active_blogs);
	print "</div>";
	print "</div>";
	
?>
<div class='frontpage_footer'>&nbsp;</div>
</div>
<script type="text/javascript">
    start_slideshow(1, <? print $slides; ?>, 4000);
</script>
<div class='frontpage_tabs'>
<div class='frontpage_tab2'>	
<div class='tab_padding'>
<?
print "<div class='tab_title'>Popular posts this week</div>";
	
$posts = get_top_posts($safe_category, $last_week, 5);
print "<div class='popular'>";
foreach ($posts as $post) {
	print_post($post, array("image" => true));
}

print "<div class='read_more'><a href='".linkto("posts.php", $page_vars, array("order_by" => "cited", "timeframe" => "1w"))."'>read more recently popular posts...</a></div>";
print "</div>";
?>	
</div>
</div>
<div class='frontpage_tab2'>
<div class='tab_padding'>
<?
	$papers = get_top_papers($safe_category, $last_fortnight);
	if (($config['collect_papers']) && (sizeof($papers))) {
		print "<div class='tab_title'>Popular papers this week</div>";
		
		foreach ($papers as $paper) {print_paper($paper, array("display" => "minimal", "show_byline" => true));}
		print "<div class='read_more'><a href='".linkto("papers.php", $page_vars, array("order_by" => "cited", "timeframe" => "1m"))."'>read more recently popular papers...</a></div>";
		print "</div>";
	} else {
		print "<div class='tab_title'>Popular posts this month</div>";
		$posts = get_top_posts($safe_category, $last_month, 5);
		print "<div class='popular'>";
		foreach ($posts as $post) {
			print_post($post, array("image" => true));
		}

		print "<div class='read_more'><a href='".linkto("posts.php", $page_vars, array("order_by" => "cited", "timeframe" => "1w"))."'>read more recently popular posts...</a></div>";
		print "</div>";
	}
?>
</div>
</div>
</div>	
<div class='frontpage_footer'>&nbsp;</div>
</div>
<? include("footer.php"); ?>