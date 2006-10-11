<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?
	# we're expecting:
	#
	# $PAGE_TITLE (to set the page title)
	# $PAGE_TYPE (to set relevant RSS feeds and Javascript includes)
	
	if (!$PAGE_TITLE) {$PAGE_TITLE = "Postgenomic";}
	if ($safe_category) {
		$PAGE_TITLE .= " - ".$safe_category;
	}
	if (!$PAGE_TYPE) {$PAGE_TYPE = "index";}

?>
<html>
<head profile="http://a9.com/-/spec/opensearch/1.1/">
	<title><? if ($title) {print $title;} else {print $PAGE_TITLE;} ?></title>
	<link type="application/opensearchdescription+xml" rel="search" title="Postgenomic" href="<? print $config['base_url']; ?>opensearch_xml.php"/>
	<link rel="stylesheet" type="text/css" href="pg.css"/>	
	<link rel="stylesheet" type="text/css" href="lightbox.css"/>
	<!-- tinymce must always be loaded before script.aculo.us -->
	<!--
	<script type="text/javascript" src="javascripts/tiny_mce/tiny_mce.js"></script>
	-->
	<!-- for fancy effects... -->
	<script type="text/javascript" src="javascripts/prototype.js"></script>
	<script type="text/javascript" src="javascripts/scriptaculous.js"></script>	
	<script type="text/javascript" src="javascripts/lightbox.js"></script>
	<script type="text/javascript" src="javascripts/postgenomic.js"></script>
	
	<link rel='alternate' type='application/atom+xml' title='Postgenomic blog' href='http://www.postgenomic.org/blog/atom.xml'/>
<?	
	feedbox("Latest posts, all categories", "atom.php?type=latest_posts", true);
	feedbox("Latest papers, all categories", "atom.php?type=latest_papers", true); 
	feedbox("Latest links (min 2 blogs), all categories", "atom.php?type=latest_links&min_links=2", true);		
?>

</head>
<body>	
<div class='title_banner'>

	<div class='title_category'>
	Explore 
	<select id='category_select' name='category' onchange='javascript:location = "<?
	
	if ($PAGE_TYPE == "posts") {
		plinkto("posts.php");
	}
	else if ($PAGE_TYPE == "links") {
		plinkto("links.php");
	}
	else if ($PAGE_TYPE == "papers") {
		plinkto("papers.php");
	}
	else if ($PAGE_TYPE == "blogs") {
		plinkto("blogs.php");
	}
	else if ($PAGE_TYPE == "stats") {
		plinkto("stats.php");
	}
	else if ($PAGE_TYPE == "news") {
		plinkto("news.php");
	}
	else {
		plinkto("index.php");
	}
	
	?>?category="+ this.options[this.selectedIndex].value'>
		<option>Any</option>
	<?
		foreach ($global_categories as $tag) {
			$selected = "";
			if ($safe_category == $tag) {$selected = "selected";}
			printf("<option %s value='%s'>%s</option>", $selected, $tag, $tag);
		}
	?>
	</select>

	</div>
	
<div class='title_logo'>
<a href='<? plinkto("index.php"); ?>'>pg</a> <span class='title_subheading'><? print strtolower($safe_category); ?></span>
</div>
</div>
<div class='title_menu'>
<div class='title_login'>
<?
	if ($logged_on) {
		print "Logged on as $logged_on (<a href='".linkto("index.php", $page_vars, array("logout" => true))."'>log out</a>)";
	} else {
		#print "<a href='".linkto("login.php", $page_vars)."'>log in or register</a>";
	}
?>
</div>
<?
	function print_menu_item($page, $page_title, $page_type = false) {
		global $page_vars;
		global $PAGE_TYPE;
		$selected = "";
		if ($PAGE_TYPE == $page_type) {
			$selected = "class='tab_selected'";
		}
		print "<a $selected href='".linkto($page, $page_vars)."'>$page_title</a>&nbsp;";
	}

	print_menu_item("index.php", "Home", "index");
	if ($config['collect_papers']) {print_menu_item("papers.php", "Papers", "papers");}
	if ($config['collect_links']) {print_menu_item("links.php", "Links", "links");}
	print_menu_item("posts.php", "Posts", "posts");
	print_menu_item("blogs.php", "Blogs", "blogs");
	if ($config['do_search']) {print_menu_item("search.php", "Search", "search");}
	print_menu_item("stats.php", "Zeitgeist", "zeitgeist");
	print_menu_item("wiki/doku.php", "Wiki", "wiki");
	
	if ($logged_on && $is_admin) {
?>
<a href='<? plinkto("admin.php", $page_vars); ?>'>Admin</a>
<?		
	}
?>
</div>
<!--[if gte IE 5]>
<style>
table {width: auto;}
</style>
<![endif]-->
<?
	# caching - some pages should be cached where possible as they don't change until the pipeline is run again.
	#$PAGE_CACHE = 0;
	$PAGE_URL = $_SERVER['REQUEST_URI'];
	if ($PAGE_CACHE) {
		$cached = get_cache($PAGE_URL);
		ob_flush(); flush();		
		if ($cached) {print $cached; exit;}
		ob_start();
	}
?>
<div class='layout'>