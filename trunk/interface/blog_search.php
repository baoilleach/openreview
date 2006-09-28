<? include("functions.php"); ?>
<?
	$PAGE_TYPE = "blogs";
	$PAGE_TITLE = "Postgenomic - Blog details";
?>
<? include("header.php"); ?>
<? include("blogs_menu.php"); ?>
<?
	$safe_blog_id = mysql_escape_string($_GET['blog_id']);
?>
<div class='sidebar'>
<?
	print "<h3>Recent Terms</h3>";
	$terms = get_terms_for_blog($safe_blog_id, 50);
	print_termcloud($terms);
	
	print_blog_stats($safe_blog_id);
?>	
</div>
<div class='content'>
<?	
	if ($safe_blog_id) {
		print "<div class='blogbox'>";
		$details = get_blogs(array($safe_blog_id), array("show_new_blogs" => true));
		$details = $details[0];
		if ($details) {
			print_blog($details, array("magnify" => true, "tagcloud" => true));			
		} else {
			print_error("Couldn't get blog details", "Sorry, I couldn't retrieve the details of the blog that you're looking for.");
		}
		
		
		$posts = get_posts("published_on", array("blog_id" => $safe_blog_id, "limit" => 3));
		if ($posts) {
			print "<h3>Most recent posts</h3>";
			foreach ($posts as $post) {
				print_post($post);
			}		
		}
		
		print "<div class='read_more'><a href='".linkto("posts.php", $page_vars, array("order_by" => "published_on"))."'>read more posts...</a></div>";
				
		$posts = get_posts("cited", array("blog_id" => $safe_blog_id, "limit" => 3));
		
		if ($posts) {
			print "<div class='popular'>";
			print "<h3>Most popular posts</h3>";
			foreach ($posts as $post) {
				print_post($post);
			}
			print "</div>";
		}
		
		$posts = array();
		
		$post_ids = get_posts_linking_to(false, $safe_blog_id);
		
		if ($post_ids) {
			print "<h3>Latest posts linking here</h3>";
			$posts = get_posts("published_on", array("post_id" => $post_ids, "limit" => 3));
			foreach ($posts as $post) {
				print_post($post);
			}
		}
	} else {
		print_error("No blog specified", "Sorry, I'm not sure which blog you're looking for.");
	}
	
?>
</div>
<? include("footer.php");?>