<? include("functions.php"); ?>
<?
	$PAGE_TYPE = "suggest";
	$PAGE_TITLE = "Postgenomic - Suggest blogs";
?>
<? include("header.php"); ?>
<div class='content'>
<?
	$connotea_tags = false;
	# suggest blogs based on your profiles on other sites (Connotea, at the moment.)	
	if ($_SAFE['connotea_username']) {
		# get tags used by connotea_username
		$connotea_tags = connotea_get_tags_for_user($_SAFE['connotea_username']);
		if (!$connotea_tags) {
			print "<p>Couldn't get results from Connotea. Give it thirty seconds then try again.";
		}
	}
?>
<h3>Connotea</h3>
<table>
<tr>
<td width='80' valign='top'>
<img src='images/comment_connotea.jpg'>
</td>
<td width='*'>
<form method='get' action='suggest.php'>
<p>Username: <input type='text' name='connotea_username' value='<? print $_SAFE['connotea_username']; ?>'/>
<p><input name='use_weighting' type='checkbox' <? if ($_SAFE['use_weighting']) {print "checked";} ?>> Use tag weighting (tags used more frequently in your library are weighted higher)
<p><input type='submit' value='Go'/>
</form>	
</td>
</tr>
</table>
<?
	$blog_ids = array();
	$blog_names = array();
	$blog_tags = array();

	if ($connotea_tags) {
		$valid_tags = validate_tags(array_keys($connotea_tags));
		
		if (!$valid_tags) {
			print "<h3>Sorry</h3>";
			print "<p>None of the tags in ".$_SAFE['connotea_username']."'s library are in the Postgenomic index.";
			print_r($connotea_tags);
		} else {
			print "<h3>Matched Connotea tags</h3>";
			print_tagcloud($valid_tags);

			# try and match up tags with blogs in the database
			foreach ($connotea_tags as $tag => $freq) {
				$post_ids = get_posts_with_tag($tag);

				# what blogs are these posts from?
				if ($post_ids) {
					$done = array();
					$posts = get_posts("cited", array("post_id" => $post_ids));
					if ($posts) {
						foreach ($posts as $post) {
							if ($done[$post['blog_id']]) {next;} else {

								if ($_SAFE['use_weighting']) {
									$blog_ids[$post['blog_id']] += $freq;
								} else {
									$blog_ids[$post['blog_id']]++;								
								} 

								$blog_names[$post['blog_id']] = $post['blog_name'];
								if ($blog_tags[$post['blog_id']]) {
									$blog_tags[$post['blog_id']] .= "|TAG|";
								}
								$blog_tags[$post['blog_id']] .= "$tag";
								$done[$post['blog_id']] = true;
							}
						}
					}
				}		
			}
		}
	}
	
	arsort($blog_ids, SORT_NUMERIC);
	print "<h3>Recommended blogs</h3>";
	foreach ($blog_ids as $blog_id => $weight) {
		print "<div class='blogbox'>";
		print "<div class='blogbox_title'><a href='".linkto("blog_search.php", $page_vars, array("blog_id" => $blog_id))."'>".$blog_names[$blog_id]."</a></div>";
		print "<div class='tagbox'>";
		$tags = explode("|TAG|", $blog_tags[$blog_id]);
		foreach ($tags as $tag) {
			print "<a href='".linkto("tag_search.php", $page_vars, array("tag" => $tag))."'>$tag</a> ";
		}
		print "</div></div>";
	}
?>


</div>
<?
include("footer.php");
?>