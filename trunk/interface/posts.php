<? include("functions.php"); ?>
<?
	$PAGE_TYPE = "posts";
	$PAGE_TITLE = $config["name"]." - Posts";
	$PAGE_CACHE = 1;
?>
<? include("header.php"); ?>
<? include("posts_menu.php"); ?>
<?
	$output_available = 1;
	$filters = array();
	$safe_timeframe = mysql_escape_string($_GET['timeframe']);
	$safe_min_links = mysql_escape_string($_GET['min_links']);
	$safe_tag = mysql_escape_string($_GET['tag']);
	
	if (!$safe_tag) {$safe_tag = false;}
	if (!$safe_order_by) {$safe_order_by = "published_on";}
	if (!in_array($safe_order_by, array("post_freq", "added_on", "cited", "published_on"))) {$safe_order_by = "added_on";}

	if (!in_array($safe_timeframe, array("3m", "1w", "1m", "1y", "10y"))) {
		$safe_timeframe = "10y";
	}
	
	if (!$safe_timeframe) {$safe_timeframe = "1m";}
	if ((!$safe_min_links) || (!is_numeric($safe_min_links))) {$safe_min_links = 0;}
	
	# $safe_pubdate_start = date("Y-m-d", mktime(0,0,0, date(m), date(d),date(Y))); 

	if ($safe_timeframe == "1w") {$safe_pubdate_start = date("Y-m-d", mktime(0,0,0, date(m), date(d) - 7,date(Y))); }	
	if ($safe_timeframe == "1m") {$safe_pubdate_start = date("Y-m-d", mktime(0,0,0, date(m) - 1, date(d),date(Y))); }
	if ($safe_timeframe == "3m") {$safe_pubdate_start = date("Y-m-d", mktime(0,0,0, date(m) - 3, date(d),date(Y))); }
	if ($safe_timeframe == "1y") {$safe_pubdate_start = date("Y-m-d", mktime(0,0,0, date(m), date(d),date(Y) - 1)); }
	if ($safe_timeframe == "10y") {$safe_pubdate_start = date("Y-m-d", mktime(0,0,0, date(m), date(d),date(Y) - 10)); }
			
	$safe_journal = mysql_escape_string($_GET['journal']);
	if ($safe_journal == "false") {$safe_journal = false;}
	$safe_term = mysql_escape_string($_GET['term']);
	$safe_skip = false;
	$safe_skip = mysql_escape_string($_GET["skip"]);
	if (!is_numeric($safe_skip)) {$safe_skip = false;}
	if (!$safe_term) {$safe_term = false;}
		
	if ( ($safe_category) && ($safe_category != "Any") ) {$filters['category'] = $safe_category;}
	
	$filters['limit'] = $GLOBALS["config"]['posts_per_page'];
	
	if ($safe_journal) {$filters['journal'] = $safe_journal;}	
	if ($safe_skip) {$filters['skip'] = $safe_skip;}
	
	$filters['published_after'] = $safe_pubdate_start;
	$filters['min_links'] = $safe_min_links;
	if ($safe_tag == "review") {
		# we're looking for posts with reviews...
		$filters['review'] = true;
	} else if ($safe_tag == "conference") {
			# we're looking for posts with reviews...
			$filters['conference'] = true;
	} else {
		$filters['tag'] = $safe_tag;
	}
	
	$page_vars['tag'] = $safe_tag;
	
	
	# terms are now handled by the code below...
	#$filters['term'] = $safe_term;
	
	$page_vars['term'] = $safe_term;
	if ($safe_term) {
		$pterms = explode(",", $safe_term);
		$tposts = array();
					
		if (sizeof($pterms)) {
			foreach ($pterms as $pterm) {
				$tposts = array_merge($tposts, get_posts_with_term($pterm));
			}
		}
		
		if (sizeof($tposts) >= 1) {
			$filters['post_id'] = $tposts;
		} else {
			$filters['post_id'] = array();
			$output_available = 0;
		}
	}
	
	if ($safe_journal) {$page_vars["journal"] = $safe_journal;}
	if ($safe_min_links) {$page_vars['min_links'] = $safe_min_links;}
	if ($safe_timeframe) {$page_vars['timeframe'] = $safe_timeframe;}
?>
<div class='sidebar'>
	<div class='sidebox'>
	<div class='sidebox_title'>Limits</div>
	<div class='sidebox_content'>
	<p>Sort by <a href='<? plinkto("posts.php", $page_vars, array("order_by" => "published_on")); ?>' <? if ($safe_order_by == "published_on") {print "class='selected'";} ?>>date published</a>
	or <a href='<? plinkto("posts.php", $page_vars, array("order_by" => "cited")); ?>' <? if ($safe_order_by == "cited") {print "class='selected'";} ?>>popularity</a>
	
	<p>Only show posts published in the last...<br/>
	<a <? if ($safe_timeframe == "1w") {print "class='selected'";}?> href='<? plinkto("posts.php", $page_vars, array("timeframe" => "1w")); ?>'>week</a>,
	<a <? if ($safe_timeframe == "1m") {print "class='selected'";}?> href='<? plinkto("posts.php", $page_vars, array("timeframe" => "1m")); ?>'>month</a>,
	<a <? if ($safe_timeframe == "3m") {print "class='selected'";}?> href='<? plinkto("posts.php", $page_vars, array("timeframe" => "3m")); ?>'>quarter</a>,
	<a <? if ($safe_timeframe == "1y") {print "class='selected'";}?> href='<? plinkto("posts.php", $page_vars, array("timeframe" => "1y")); ?>'>year</a> or
	<a <? if ($safe_timeframe == "10y") {print "class='selected'";}?> href='<? plinkto("posts.php", $page_vars, array("timeframe" => "10y")); ?>'>show all posts</a>	
</div>
</div>

<div class='sidebox'>
<div class='sidebox_title'>Subscribe</div>
<div class='sidebox_content'>
		<? 
		if ($safe_category) {
			print "<p>Subscribe to posts in the ".strtolower($safe_category)." category:";
		}

		feedbox("Latest posts", "atom.php?category=$safe_category&type=latest_posts"); 		
		feedbox("Recently popular posts", "atom.php?category=$safe_category&type=popular_posts"); 		
		feedbox("Latest reviews", "atom.php?category=$safe_category&type=latest_posts&tag=review"); 		
		feedbox("Latest conference reports", "atom.php?category=$safe_category&type=latest_posts&tag=conference"); 	
		feedbox("Latest original research", "atom.php?category=$safe_category&type=latest_posts&tag=original_research"); 	
	?>
</div>
</div>
<?
	print_searchbox("Posts");
?>
</div>
<div class='content'>
<?	
	if ($output_available) {
		$posts = get_posts($safe_order_by, $filters);
	}
	
	# pagination control
	if ($posts) {
		print_pagination($posts, $safe_skip, "posts.php", $GLOBALS["config"]['posts_per_page']);
		foreach ($posts as $post) {
			$display_filters = array("image" => true);
			if ($filters['conference']) {
				$display_filters["display_geotags"] = true;
			}
			print_post($post, $display_filters);
		}
		print_pagination($posts, $safe_skip, "posts.php", $GLOBALS["config"]['posts_per_page']);		
	} else {
		print "No posts found.";
	}

?>
</div>
<? include("footer.php"); ?>