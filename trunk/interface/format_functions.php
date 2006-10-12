<?

function slinkto($page, $vars = array(), $extras = array()) {
	global $config;
	return $config['base_url'].linkto($page, $vars, $extras);
}

function plinkto($page, $vars = array(), $extras = array()) {
	print linkto($page, $vars, $extras);
}

function linkto($page, $vars = array(), $extras = array()) {
	$link = "$page";
	
	# note that extras overwrite any vars that they share keys with
	if ($extras) {
		foreach ($extras as $key => $value) {
			$vars[$key] = $value;
		}
	}
	
	if ($vars) {
		$counter = 0;	
		foreach ($vars as $key => $value) {
			if (strlen($value) >= 1) {
				if ($counter >= 1) {$link .= "&";} else {$link .= "?";}
				$link .= "$key=$value";
				$counter++;
			}
		}
	}
	
	return $link;
}

function tabulate_tags($tags, $field = "count") {
	global $page_vars;
	
	$counter = 0;
	print "<div class='stats_table'>";
	foreach ($tags as $tag) {
		$row = "even_row";
		if ($counter % 2) {$row = "odd_row";}
		printf("<div class='$row'>%d <a href='%s'>%s</a></div>", $tag[$field], linkto("tag_search.php", $page_vars, array("tag" => $tag['tag'])), $tag['tag']);
		$counter++;
	}
	print "</div>";
}

function tabulate_journals($journals, $field = "rank") {
	global $page_vars;
	
	$counter = 0;
	print "<div class='stats_table'>";
	foreach ($journals as $journal) {
		$row = "even_row";
		if ($counter % 2) {$row = "odd_row";}
		printf("<div class='$row'>%d <a href='%s'>%s</a></div>", $journal[$field], linkto("journal_search.php", $page_vars, array("journal_id" => $journal['journal'])), $journal['journal']);
		$counter++;
	}
	print "</div>";
}

function tabulate_blogs($blogs, $field = "rank") {
	global $page_vars;
	
	$counter = 0;
	print "<div class='stats_table'>";
	foreach ($blogs as $blog) {
		$row = "even_row";
		if ($counter % 2) {$row = "odd_row";}
		printf("<div class='$row'>%d <a href='%s'>%s</a></div>", $blog[$field], linkto("blog_search.php", $page_vars, array("blog_id" => $blog['blog_id'])), $blog['title']);
		$counter++;
	}
	print "</div>";
}

function convert_fog($fog) {
	$fog_equivalent = "TV Guide";
	
	if ($fog >= 0) {$fog_equivalent = "TV Guide";}
	if ($fog >= 6) {$fog_equivalent = "Harriet the Spy";}
	if ($fog >= 7) {$fog_equivalent = "Stephen King";}
	if ($fog >= 8) {$fog_equivalent = "Dan Brown";}
	if ($fog >= 10) {$fog_equivalent = "Newsweek";}
	if ($fog >= 11) {$fog_equivalent = "Thomas Pynchon";}
	if ($fog >= 13) {$fog_equivalent = "Al Gore";}
	if ($fog >= 14) {$fog_equivalent = "The Guardian";}
	if ($fog >= 15) {$fog_equivalent = "Scientific journals";}
	if ($fog >= 17) {$fog_equivalent = "Manual for Taiwanese DVD player";}

	return $fog_equivalent;
}

function print_blog_stats($blog_id) {
	$stats = get_blog_stats($blog_id);
	if (!sizeof($stats)) {return;}

	$fog_equivalent = convert_fog($stats['readability_fog']);

	if ($stats['readability_kincaid'] > 12) {$stats['readability_kincaid'] = 12;}

	printf("
<h3>Stats</h3>
<p>Statistics are based on posts from the past ninety days.
<p><br/>
<div class='statsbox'>
<h2># %s</h2> rank
<h2>%s</h2> posts
<h2>%s</h2> words per post
<h2>%.2f%%</h2> percent complex words
<a href='http://en.wikipedia.org/wiki/Readability'><h2>%.1f</h2> Flesch reading ease</a>
<a href='http://en.wikipedia.org/wiki/Readability'><h2>%.1f</h2> <h4>(Grade %d)</h4> Flesch-Kincaid grade level</a>
<a href='http://en.wikipedia.org/wiki/Gunning-Fog_Index'><h2>%.1f</h2> <h4>(%s)</h4> Gunning-Fog index</a>
<h2>%s</h2> incoming bloglove
<h2>%s</h2> outgoing bloglove
<h2>%s</h2> incoming links
<h2>%s</h2> outgoing links
</div>
",
	number_format($stats['rank']),
	number_format($stats['num_posts']),
	number_format($stats['avg_words_per_post']),
	$stats['percent_complex_words'],
	$stats['readability_flesch'],
	$stats['readability_kincaid'],
	ceil($stats['readability_kincaid']),
	$stats['readability_fog'],
	$fog_equivalent,
	number_format($stats['incoming_bloglove']),
	number_format($stats['outgoing_bloglove']),
	number_format($stats['incoming_links']),
	number_format($stats['outgoing_links'])
);
}

function get_blog_stats($blog_id) {
	$query = "SELECT * FROM blog_stats WHERE blog_id='$blog_id'";
	$results = mysql_query($query);
	$return = array();
	while ($row = mysql_fetch_assoc($results)) {
		$return = $row;
	}
	return $return;
}

function print_journal_stats($journal, $stats = array()) {
	if (!sizeof($stats)) {$stats = get_journal_stats($journal);}
	if (!sizeof($stats)) {return;}
	
		printf("
	<h3>Stats</h3>
	<div class='statsbox'>
	<h2># %s</h2> rank
	<h2>%s</h2> papers
	<h2>%s</h2> incoming bloglove
	<h2>%s</h2> incoming links
	</div>
	",
		number_format($stats['rank']),
		number_format($stats['num_papers']),
		number_format($stats['incoming_bloglove']),
		number_format($stats['incoming_links'])
	);	
}

function get_journal_stats($journal) {
	$query = "SELECT * FROM journal_stats WHERE journal='$journal'";
	$results = mysql_query($query);
	$return = array();
	while ($row = mysql_fetch_assoc($results)) {
		$return = $row;
	}
	return $return;
}

function print_system_stats($category = false) {
	global $page_vars;
	
	$stats = get_system_stats($category);
	
	$results = sprintf("
<div class='statsbox'>
<a href='%s'><h2>%s</h2> blogs</a>
<a href='%s'><h2>%s</h2> posts</a>
<a href='%s'><h2>%s</h2> papers</a>
</div>	
	", 
	linkto("blogs.php", $page_vars), number_format($stats['blogs']), 
	linkto("posts.php", $page_vars), number_format($stats['posts']), 
	linkto("papers.php", $page_vars), number_format($stats['papers']));

	print $results;
}

function get_system_stats($category = false) {	
	$stats = array();
	$blogs = array();
	if ($category) {$blogs = get_blogs_with_tag($category);}
	$catclause = "";
	if (sizeof($blogs)) {$catclause = " AND blog_id IN ('".implode("','", $blogs)."')";}
			
	$query = "SELECT COUNT(DISTINCT post_id) AS post_count, COUNT(DISTINCT blog_id) AS blog_count FROM posts WHERE !ISNULL(blog_id) $catclause";

	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$stats["posts"] = $row['post_count'];
		$stats["blogs"] = $row['blog_count'];
	}
	
	$query = "SELECT COUNT(DISTINCT paper_id) AS paper_count FROM links WHERE !ISNULL(paper_id) $catclause";
	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$stats["papers"] = $row['paper_count'];
	}
	
	return $stats;
}

function print_blogger_slides($category = false) {
	$catclause = "";
	if ($category) {
		$blogs = get_blogs_with_tag($category);
		if ($blogs) {
			$catclause = " AND blog_id IN ('".implode("','", $blogs)."')";
		}
	}
	
	$query = "SELECT blog_image AS image, blog_id, blog_name AS title, title AS post_title, url AS post_url FROM posts_summary WHERE blog_image NOT LIKE '%default.png' $catclause ORDER BY pubdate DESC LIMIT 25";
	$results = mysql_query($query);

	$counter = 1;
	while ($row = mysql_fetch_array($results)) {
		if ($counter > 1) {$display = "style='display: none;'";}
		$blog_link = linkto("blog_search.php", $page_vars, array("blog_id" => $row["blog_id"]));
		print "\n<div id='slideshow$counter' class='slide' $display><div>";
		print "<a href='$blog_link'><img border='0' hspace='5' vspace='0' alt='".$row['title']."' src='".$row['image']."' align='left'/></a><a href='$blog_link'>".$row['title']."</a>
		<p><a href='".$row['post_url']."'><b>".$row['post_title']."</b></a>";
		print "</div></div>";
		$counter++;
	}
	
	return ($counter - 1);
}

function print_bloggercloud($category = false) {
	$catclause = "";
	if ($category) {
		$blogs = get_blogs_with_tag($category);
		if ($blogs) {
			$catclause = " AND blog_id IN ('".implode("','", $blogs)."')";
		}
	}
	
	$query = "SELECT DISTINCT blog_image AS image, blog_id, blog_name AS title FROM posts_summary WHERE blog_image NOT LIKE '%default.png' $catclause ORDER BY pubdate DESC LIMIT 5";
	$results = mysql_query($query);

	$images = array();
	
	while ($row = mysql_fetch_array($results)) {
		array_push($images, $row);
	}


	print "<div style='width: 320px;'>";
	#print "<div>";
	for ($i=0; $i < 5; $i++) {
		if (!$images[$i]) {continue;}
		if (!($i % 4)) {
			#print "</div><div>";
		}
		print "<a href='".linkto("blog_search.php", $page_vars, array("blog_id" => $images[$i]["blog_id"]))."'><img border='0' hspace='0' vspace='0' alt='".$images[$i]['title']."' src='".$images[$i]['image']."'/></a>";
	}
	#print "</div>";
	print "</div>";
}

function print_journalcloud($journals, $limit = false) {
	# tags must be an assoc array, where the key is the tag and the value is the number of occurrences
	# get the highest count and the lowest, then create a fixed number of bins
	$tags = $journals;
	
	$bins = 4;
	
	$values = array_values($tags);
	rsort($values);

	$highest = $values[0];
	$lowest = 1;
	if ($limit) {$lowest = $values[$limit];}
		
	$binsize = floor(($highest - $lowest) / $bins);
	
	if ($binsize == 0) {$binsize = 1;}
	
	$counter = 0;
	print "<div class='tagcloud'>";
	foreach ($tags as $key => $value) {
		if ( ($limit) && ($value < $values[$limit]) ) {continue;}  
		if ($counter >= $limit) {continue;}
		
		$bin = floor($value / $binsize);
		if ($bin > $bins) {$bin = $bins;} 
		print "<span class='tagcloud_$bin'><a href='".linkto("journal_search.php", $GLOBALS['page_vars'], array("journal_id" => $key))."'>".substr($key,0,24)."</a></span> ";
		$counter++;
	}
	print "</div>";
}

function print_blogcloud($active_blogs) {
	# tags must be an assoc array, where the key is the tag and the value is the number of occurrences
	# get the highest count and the lowest, then create a fixed number of bins
	$tags = $active_blogs["blogs"];
	$mapping = $active_blogs["mapping"];
	
	$bins = 4;
	
	$values = array_values($tags);
	rsort($values);

	$highest = $values[0];
	$lowest = 1;
	
	$binsize = floor(($highest - $lowest) / $bins);
	
	if ($binsize == 0) {$binsize = 1;}
	
	print "<div class='tagcloud'>";
	foreach ($tags as $key => $value) {
		$bin = floor($value / $binsize);
		if ($bin > $bins) {$bin = $bins;} 
		print "<a class='tagcloud_$bin' href='".linkto("blog_search.php", $GLOBALS['page_vars'], array("blog_id" => $key))."'>".$mapping[$key]."</a> ";
	}
	print "</div>";
}

function print_tagcloud($tags) {
	# tags must be an assoc array, where the key is the tag and the value is the number of occurrences
	# get the highest count and the lowest, then create a fixed number of bins
	$bins = 4;
	
	$values = array_values($tags);
	rsort($values);

	$highest = $values[0];
	$lowest = 1;
	
	$binsize = floor(($highest - $lowest) / $bins);
	
	if ($binsize == 0) {$binsize = 1;}
	
	print "<div class='tagcloud'>";
	foreach ($tags as $key => $value) {
		$bin = floor($value / $binsize);
		if ($bin > $bins) {$bin = $bins;} 
		print "<a class='tagcloud_$bin' href='".linkto("tag_search.php", $GLOBALS['page_vars'], array("tag" => $key))."'>$key</a> ";
	}
	print "</div>";
}

function print_termcloud($tags, $prefs = array()) {
	# tags must be an assoc array, where the key is the tag and the value is the number of occurrences
	# get the highest count and the lowest, then create a fixed number of bins
	$bins = 4;
	
	$values = array_values($tags);
	rsort($values);

	$highest = $values[0];
	$lowest = 1;
	
	$binsize = floor(($highest - $lowest) / $bins);
	
	if ($binsize == 0) {$binsize = 1;}
	
	print "<div class='tagcloud'>";
	foreach ($tags as $key => $value) {
		$bin = floor($value / $binsize);
		if ($bin > $bins) {$bin = $bins;} 
		$target = "";
		if ($prefs['target']) {$target = "target='".$prefs['target']."'";}
		print "<a class='tagcloud_$bin' $target href='".linkto("posts.php", $GLOBALS['page_vars'], array("term" => $key, "order_by" => "cited", "category" => "false"))."'>$key</a> ";
	}
	print "</div>";
}

function print_error($title, $text) {
	print "<div class='errorbox'>";
	print "<h1>$title</h1>";
	print "<p>$text";
	print "<p><a href='".linkto("index.php", $GLOBALS['page_vars'])."'>Return to front page</a>";
	print "</div>";
}

function print_pagination($papers = array(), $safe_skip = 0, $link_to = "papers.php", $rows_per_page = 10, $other_vars = array()) {
	print "<div class='pagination'>";
	if (is_numeric($papers)) {
		# bit of a hack: support pagination when there are multiple content types by passing rows_returned
		# directly
		$rows = $papers;
	} else {
		$rows = $papers[0]["rows_returned"];
	}
	
	$on_page = 1;
	if ($safe_skip) {$on_page = floor(($safe_skip / $rows_per_page) + 1);}
	$pages = ceil($rows / $rows_per_page);
	if ($on_page > $pages) {$on_page = $pages;}
	if ($on_page < 1) {$on_page = 1;}
	print "<span class='pagebox_rows'>".number_format($rows)." total</span> ";	
	
	$start_page = $on_page - 5;
	$end_page = $on_page + 5;

	if ($pages > 10) {
		while ($start_page < 1) {
			$start_page++;
			$end_page++;
		}
		while ($end_page > $pages) {
			$end_page--;
			$start_page--;
		}
	} else {
		$start_page = 1;
		$end_page = $pages;
	}

	if ($on_page > 1) {
		$filters = $other_vars;
		$filters["skip"] = 0;
		print "<a class='pagebox' href='".linkto($link_to, $GLOBALS["page_vars"], $filters)."'>&lt;&lt;</a>";	
		
		$filters = $other_vars;
		$filters["skip"] = (($on_page - 2) * $rows_per_page);
		print "<a class='pagebox' href='".linkto($link_to, $GLOBALS["page_vars"], $filters)."'>&lt;</a>";
	}
	
	for ($page = $start_page; $page <= $end_page; $page++) {
		if ( ($page < 1) || ($page > $pages) ) {
			print "<a class='pagebox'>..</a>";
		} else {
			$class = 'pagebox';
			if ($on_page == $page) {$class .= " current_pagebox";}
			$filters = $other_vars;
			$filters["skip"] = (($page - 1) * $rows_per_page);
			print "<a class='$class' href='".linkto($link_to, $GLOBALS["page_vars"], $filters)."'>$page</a>";
		}
	}
	
	if ($on_page < $pages) {
		$filters = $other_vars;
		$filters["skip"] = (($on_page) * $rows_per_page);
		print "<a class='pagebox' href='".linkto($link_to, $GLOBALS["page_vars"], $filters)."'>&gt;</a>";
		
		$filters = $other_vars;
		$filters["skip"] = (($pages - 1) * $rows_per_page);
		print "<a class='pagebox' href='".linkto($link_to, $GLOBALS["page_vars"], $filters)."'>&gt;&gt;</a>";
	}
	print "</div>";	
}

# print paper with paper_id $id
function print_paper($paper, $filters = array()) {
	global $logged_on;
	
	$paper_id = $paper['paper_id'];
	
	print "<div class='paperbox'>";
		
	# some shortcuts:
	if ($filters['display'] == "minimal") {
		if (!isset($filters['show_byline'])) {$filters['show_byline'] = true;}
		if (!isset($filters['show_tags'])) {$filters['show_tags'] = true;}	
		if (!isset($filters['show_posts'])) {$filters['show_posts'] = false;}
		if (!isset($filters['show_abstract'])) {$filters['show_abstract'] = false;}	
		if (!isset($filters['show_comments'])) {$filters['show_comments'] = false;}
		if (!isset($filters['show_teaser'])) {$filters['show_teaser'] = false;}
		if (!isset($filters['show_scorebox'])) {$filters['show_scorebox'] = true;}
		if (!isset($filters['add_comment'])) {$filters['add_comment'] = false;}
	} else {
		# set some defaults
		if (!isset($filters['show_byline'])) {$filters['show_byline'] = true;}
		if (!isset($filters['show_tags'])) {$filters['show_tags'] = true;}	
		if (!isset($filters['show_posts'])) {$filters['show_posts'] = true;}
		if (!isset($filters['show_abstract'])) {$filters['show_abstract'] = true;}	
		if (!isset($filters['show_comments'])) {$filters['show_comments'] = true;}
		if (!isset($filters['show_teaser'])) {$filters['show_teaser'] = false;}
		if (!isset($filters['show_scorebox'])) {$filters['show_scorebox'] = true;}
		if (!isset($filters['add_comment'])) {$filters['add_comment'] = true;}
	}
		
	$tags = array();
	
	if ($filters['show_tags'] == true) {
		$tags = get_tags_for_paper($paper_id, true);
	}

	if ($filters['show_scorebox'] == true) {
		print "<a href='".linkto("paper.php", $page_vars, array("paper_id" => $paper['paper_id']))."'><div class='scorebox'>";
		print "<img border='0' src='images/comments.png'/>";
		print $paper['cited_by'];
		print "</div></a> ";
	}
	
	if (!$paper['title']) {$paper['title'] = "Unknown title";}
	print "<div class='paperbox_title'>";
	
	if ($filters['magnify']) {print "<div class='magnify'>";}
	$title_link = linkto("paper.php", $GLOBALS['page_vars'], array("paper_id" => $paper['paper_id']));
	
	if ($filters['link_through']) {$title_link = $paper['url'];}
		
	print "<a href='$title_link'>".$paper['title']."</a>";
	if ($filters['magnify']) {print "</div>";}
		
	print "</div>";
	
	if ($tags) {
		print "<div class='tagbox'>Tags: ";
		foreach ($tags as $tag) {
			print "<a href='".linkto("tag_search.php", $GLOBALS['page_vars'], array("tag" => $tag))."'>$tag</a> ";
		}
		print "</div>";
	}
	
	if ($filters['show_byline'] == true) {
		print "<div class='paperbox_byline'>";
		
		print connotea_link($paper['url']);

		print "<a href='".linkto("journal_search.php", $GLOBALS['page_vars'], array("journal_id" => $paper['journal']))."'>".$paper['journal']."</a> <span class='date'>".date("D jS M y", strtotime($paper['pubdate']))."</span> <span class='author'>".$paper['authors']."</span></div>";
	}
		
	if ($filters['show_abstract'] == true) {
		if ($paper['abstract']) {
			print "<div class='paperbox_abstract'>".$paper['abstract']."</div>";
		}
	}
	
	if ($filters['add_comment']) {
		if ($logged_on) {
			#print_comment_prompt();
		}
	}
	
	if ($filters['show_posts'] == true) {
		$posts = get_posts_for_paper($paper_id);
		
		if ($posts) {	
			print "<div class='paperbox_posts'>";
			print "<h3>Posts</h3>";

			foreach ($posts as $post) {
				print_post($post, array("image" => true));
			}
			print "</div>";
		}
	}
	
	if ($filters['show_comments'] == true) {
		$comments = get_comments_for_paper($paper_id);

		if ($comments) {
			print "<div class='paperbox_comments'>";
			print "<h3>Comments</h3>";
	
			foreach ($comments as $comment) {
				print_comment($comment);
			}
		
			print "</div>";
		}
	}
	
	print "</div>";
}

function print_comment_prompt() {
	global $logged_on;
	
	print "<div class='paperbox_prompt'>";
	print "<a style='cursor: pointer;' onclick=\"new Effect.toggle('add_comment', 'Appear', { duration: 0.5});\"><img border='0' style='margin-right: 5px;' src='images/comments_add.png'/>";
	print "Comment on this paper</a>";	
	print "<div id='add_comment' class='paperbox_add_comment' style='display: block;'>";
	print "Write your comment below and /or give this paper a rating, then press the Submit button.";
	print "<form method='POST' action='comment.php'>";
	print "<table width='100%' cellspacing='4' cellpadding='0' style='padding: 5px;'>";
	print "<tr>";
	print "<td valign='top' width='110'>";
	
	print "<div class='rating rating5'><input name='rating' style='float: left;' type='radio'> Exceptional</input></div>";
	print "<div class='rating rating4'><input name='rating' style='float: left;' type='radio'> 4</input></div>";
	print "<div class='rating rating3'><input name='rating' style='float: left;' type='radio'> 3</input></div>";
	print "<div class='rating rating2'><input name='rating' style='float: left;' type='radio'> 2</input></div>";
	print "<div class='rating rating1'><input name='rating' style='float: left;' type='radio'> Poor</input></div>";
				
	print "</td>";
	print "<td valign='top' width='*'>";
	print "<textarea name='comment' style='height: 78px; width: 100%;'></textarea>";
	print "</td>";
	print "</tr>";
	print "<tr><td colspan='2' align='right'><input type='submit' value='Submit'></input></td></tr>";
	print "</table>";
	print "</form>";
	
	print "</div>";
	print "</div>";
}

function print_comment($comment) {
	print "<div class='postbox'>";
	if (!$comment['title']) {$comment['title'] = $comment['source'];}
	print "<div class='postbox_title'><a href='".$comment['url']."'>".$comment['title']."</a></div>";
	
	# some special cases...
	if ($comment['source'] == "F1000 Biology") {
		$comment['comment'] = "The Faculty of 1000 requires a paid subscription. If you have one you can <a href='".$comment['url']."'>read the comment there</a>.";
	}
	
	# don't display entire comment
	$comment['comment'] = "<p>".substr(strip_tags($comment['comment']),0,512)."...";

	
	
	if ($comment['image']) {
		print "<div class='postbox_thumbnail'><img src='".$comment['image']."' border='0'/></div>";
	}
	print "<div class='postbox_byline'>";
	
	if ($comment['source'] != "Connotea") {
		print connotea_link($comment['url']);
	}
		
	print "Comment on <b>".$comment['source']."</b>";
	if ($comment['author']) {
		if ($comment['source'] == "Connotea") {
			print " by <a href='http://www.connotea.org/user/".$comment['author']."'>".$comment['author']."</a>";
		} else {
			print " by <b>".$comment['author']."</b>";
		}
	}
	print "</div>";
	print "<div class='postbox_content'>";
	print strip_tags($comment['comment'], '<p>');
	print "</div>";
	print "<div class='postbox_footer'>&nbsp;</div>";
	print "</div>";	
}

function print_link($link, $filters = array()) {
	print "<div class='linkbox'>";
	#print "<a href='".linkto("link_search.php", $page_vars, array("url_hash" => md5($link['url'])))."'>";
	print "<div class='scorebox'><img src='images/link.png' border='0'/>".$link['linked_by']."</div>";
	#print "</a>";	
	print "<div class='linkbox_title'>";
	if (!$link['titles']) {$link['titles'] = substr($link['url'],0,64)."...";} else {$link['titles'] = ucfirst($link['titles']);}
	print "<a href='".$link['url']."'>".$link['page_title']."</a>";
	print "</div>";
	print "<div class='linkbox_footer'>&nbsp;</div>";
	
	$link['image'] = "images/link_default.png";
	if ( ($link['image']) && ($link['linked_by'] >= 2) ) {
		print "<div class='linkbox_thumbnail'><img src='images/link_default.png'/></div>";
	}
	
	print "<div class='linkbox_byline'>";
	
	print connotea_link($link['url']);
	print " <a class='dhtml_link' onclick='showHideDiv(\"".$link['url_hash']."\")'><img src='images/comments.png' align='absmiddle' /> see which posts linked here</a>";
	print " last linked to on <b>".date("D jS M y", strtotime($link['last_linked_on']))."</b>";
	print "</div>";	
	print "<div class='linkbox_content'>";
	print "<p>".$link['titles'];
	print "<div id='".$link['url_hash']."' class='linkbox_posts'  style='display: none;'>";
	$posts = explode("|||", $link['post_titles']);
	foreach ($posts as $post) {
		$bits = explode("===", $post);
		print "<p> <a href='".$bits[1]."'>".$bits[0]."</a> from <a href='".linkto("blog_search.php", $page_vars, array("blog_id" => $bits[5]))."'>".$bits[3]."</a>";
	}
	print "</div>";
	print "</div>";
	print "</div>";
	print "<div class='linkbox_footer'>&nbsp;</div>";
}

function lightbox($url, $caption = false) {
	$return = "<div class='imagebox'><a href='$url' rel='lightbox'>[Image]";
	if ($caption) {$return .= $caption;}
	$return .= "</a></div>";
	return $return;
}

function feedbox($title, $url, $link_only = false) {
	if ($link_only) {
		print "
<link rel='alternate' type='application/atom+xml' title='$title' href='$url'/>
		";
	} else {
		print "
<div class='feedbox'>
<a href='$url'>
<img src='images/feed.png' border='0' align='texttop' /> $title	
</a>
</div>";
	}
}

function connotea_link($url) {
	return "<a style='cursor:pointer;' onclick=\"javascript:u='".$url."';a=false;x=window;e=x.encodeURIComponent;d=document;w=open('http://www.connotea.org/addpopup?continue=confirm&uri='+e(u),'add','width=400,height=400,scrollbars,resizable');void(x.setTimeout('w.focus()',200));\"><img src='images/connotea_gray.gif' style='border: 0px;' border='0' alt='add bookmark to connotea' align='absmiddle' /></a>";
}

function print_post($post, $filters = array()) {
	if ($post['type'] == "review") {print "<div class='review'>";}
	print "<div class='postbox'>";
	print "<div class='postbox_title'>";
	if (isset($post['linked_by'])) {
		print "<a href='".linkto("post.php", $page_vars, array("post_id" => $post['post_id']))."'><div class='scorebox'><img src='images/link.png' border='0'/>".$post['linked_by']."</div></a>";
	}
	
	if ($filters['magnify']) {print "<div class='magnify'>";}
	if ($post['type'] == "review") {print "Review: ";}
	print "<a href='".$post['url']."'>".$post['title']."</a>";
	if ($filters['magnify']) {print "</div>";}

	print "<div class='postbox_footer'>&nbsp;</div>";
	print "</div>";
	if ( ($post['blog_image']) && ($filters['image']) ) {
		print "<div class='postbox_thumbnail'><img src='".$post['blog_image']."'/></div>";
	}
	
	if (!$filters['short']) {
		print "<div class='postbox_byline'>";

		print connotea_link($post['url']);

		print "<a href='".linkto("blog_search.php", $GLOBALS['page_vars'], array("blog_id" => $post['blog_id']))."'>".$post['blog_name']."</a> on <span class='date'>".date("D jS M y", strtotime($post['pubdate']))."</span>";

		if ($filters['display_geotags']) {
			$geotags = get_geotags_for_post($post['post_id']);
			if ($geotags) {
				for ($i=0; $i < sizeof($geotags); $i += 3) {
					$lat = $geotags[$i];
					$lng = $geotags[($i + 1)];
					$term = $geotags[($i + 2)];

					print "<a href='http://maps.google.com/maps?f=q&hl=en&q=&ie=UTF8&ll=$lat,$lng&om=1&spn=1.793919,3.955078'><img align='absmiddle' hspace='5' src='images/world_link.png' border='0'/> $term</a>";				
				}
			}
		}

		print "</div>";
	
		print "<div class='postbox_content'>";
	
		if ($filters['fulltext']) {
			$flatfile = $GLOBALS['config']['path_to_pipeline'].$post['filename'];
			$xml = process_post_xml($flatfile);
			print $xml['description'];
		} else {
			print $post['summary']."...";
		}
		print "</div>";
	}
	print "<div class='postbox_footer'>&nbsp;</div>";
	print "</div>";
	if ($post['type'] == "review") {print "</div>";}
}

function print_blog($blog, $filters = array()) {
	global $page_vars;
	if (!$blog['active']) {
		print "<div class='blogbox blogbox_inactive'>";	
		print "<div class='blogbox_title'>";		
		print "<a href='".linkto("blog_search.php", $page_vars, array("blog_id" => $blog['blog_id']))."'>".$blog['title']."</a>";
		print "</div>";
		print "<p><i>This blog is inactive - it is no longer aggregated. <a href='".linkto("manage_blogs.php", array(), array("workspace" => $filters['workspace'], "restore_blog_id" => $blog['blog_id']))."'>Click here</a> to restore it.</i>";
		print "</div>";
		return;
	}
	
	print "<div class='blogbox'>";
	
	if (isset($blog['incoming_bloglove'])) {
		print "<a href='".linkto("blog_search.php", $page_vars, array("blog_id" => $blog['blog_id']))."'><div class='scorebox'><img border='0' style='border: 0px;' src='images/link.png'/>".$blog['incoming_bloglove'];
		
		if ($filters['rank']) {
			print "<div class='rankbox'>#".$blog['rank']."</div>";
		}
		print "</div></a>";
	}	
	print "<div class='blogbox_title'>";	
	if ($filters['magnify']) {print "<div class='magnify'>";}
	print "<a href='".linkto("blog_search.php", $page_vars, array("blog_id" => $blog['blog_id']))."'>".$blog['title']."</a>";
	if ($filters['magnify']) {print "</div>";}
	print "<div class='blogbox_footer'>&nbsp;</div>";
	print "</div>";
	print "<div class='blogbox_content'>";
	if ($blog['image']) {
		print "<img src='".$blog['image']."' align='left'/>";		
	}
	print "<p>";
	print "<a href='".$blog['feed_url']."'>";
	print "<img style='border: 0px;' src='images/feed.png' border='0' align='texttop'/> ";
	print "</a>";
	print connotea_link($blog['url']);
	print "<a href='".$blog['url']."'>".$blog['url']."</a>";

	print "<p>".strip_tags(html_entity_decode($blog['description']));
	
	if ($filters['tagcloud']) {
		print "<div class='blogbox_tags'>";
		$tags = get_tags_for_blogs(array($blog['blog_id']), 3);
		if ($tags) {print_tagcloud($tags);}
		print "</div>";
	}
	print "</div>";
	
	print "<div class='blogbox_footer'>&nbsp;</div>";
	
	print "<div class='blogbox_byline'>";
	$tags_array = get_blog_categories($blog['blog_id']);
	$tags = array();
	
	if ($filters['add_tag']) {	
 		print "<p><i>This blog is active - new posts will be aggregated. <a href='".linkto("manage_blogs.php", array(), array("workspace" => $filters['workspace'], "remove_blog_id" => $blog['blog_id']))."'>Click here</a> to delete it.</i>";		
			
		# controls that allow you to add and remove tags
		foreach ($tags_array as $tag) {
			$id = $blog['blog_id'].":".$tag;
			array_push($tags, "<a id='$id' onclick='switchClass(this);' class='tag_selected'>$tag</a>");
		}

		print "<div class='tagbox'><p>Tags: ".implode(' ', $tags);
			
		$query = "SELECT DISTINCT tag FROM tags WHERE !ISNULL(blog_id) ORDER BY tag ASC";
		$results = mysql_query($query);
		while ($row = mysql_fetch_assoc($results)) {
			$id = $blog['blog_id'].":".$row['tag'];
			if (!in_array($row['tag'], $tags_array)) {
				if ($safe_category == $row['tag']) {$selected = "selected";}
				printf(" <a id='$id' onclick='switchClass(this);' class='tag_select'>%s</a>", $row['tag']);
			}
		}
		
		$id = $blog['blog_id'].":custom";
		print "<p>Create a custom tag? <input type='textbox' id='$id' onchange='addCustomTag(this);' value=''/>";
		print "</div>";		
	} else {
		foreach ($tags_array as $tag) {
			array_push($tags, "<a href='".linkto("blogs.php", $page_vars, array("category" => $tag))."'>$tag</a>");
		}
		print "<div class='tagbox'>Categories: ".implode(' ', $tags)."</div>";
	}
	print "</div>";
		
	print "<div class='blogbox_footer'>&nbsp;</div>";
	print "</div>";
}


?>