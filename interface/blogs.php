<? include("functions.php"); ?>
<?
	$PAGE_TYPE = "blogs";
	$PAGE_TITLE = "Postgenomic - Blogs";
	$PAGE_CACHE = 1;
?>
<? include("header.php"); ?>
<? include("blogs_menu.php"); ?>
<div class='sidebar'>
<h3>Suggest</h3>
Know of a science blog that should be included here? 
<?
	$email = $GLOBALS["config"]["email"];
	
	print "<a href='mailto:$email'>Suggest it</a>";
?>
<h3>Subscribe</h3>
<?
if ($safe_category) {
	print "<p>Subscribe to new blogs from the ".strtolower($safe_category)." category:";
}

feedbox("New blogs", "atom.php?category=$safe_category&type=latest_blogs");
?>
<h3>Search</h3>
<div class='searchbox'>
<form action='search.php' method='GET'>
<input class='textbox' style='width: 140px;' type='text' name='search'/> <input type='submit' value='Search' />
<p>
<input type='radio' name='type' value='any'>Anything
<input type='radio' checked name='type' value='blogs'>Blogs
</form>
</div>
</div>
<div class='content'>
<?

	$safe_skip = false;
	$safe_skip = mysql_escape_string($_GET["skip"]);
	if (!is_numeric($safe_skip)) {$safe_skip = false;}
	
	$filters = array();
	$filters['limit'] = $GLOBALS["config"]['blogs_per_page'];
	if ($safe_skip) {$filters['skip'] = $safe_skip;} else {$filters['skip'] = 0;}
		
	$blogs = get_blogs(get_blogs_with_tag($safe_category), $filters);
	
	print_pagination($blogs, $safe_skip, "blogs.php", $GLOBALS["config"]['blogs_per_page']);
	
	foreach ($blogs as $blog) {
		print_blog($blog, array("tagcloud" => true));
	}

	print_pagination($blogs, $safe_skip, "blogs.php", $GLOBALS["config"]['blogs_per_page']);
	
?>
</div>
<? include("footer.php"); ?>