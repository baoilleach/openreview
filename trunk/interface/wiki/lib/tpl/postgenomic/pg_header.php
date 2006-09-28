<div class='title_banner'>

	<div class='title_category'>
	</div>

<div class='title_logo'>
<a href='../index.php'>pg</a> <span class='title_subheading'>wiki</span>
</div>
</div>
<div class='title_menu'>
<div class='title_login'>
<?
	if ($logged_on) {
		# print "Logged on as $logged_on (<a href='".linkto("index.php", $page_vars, array("logout" => true))."'>log out</a>)";
	} else {
		# print "<a href='".linkto("login.php", $page_vars)."'>log in or register</a>";
	}
?>
</div>
<a href='../index.php'>Home</a>
<a href='../papers.php'>Papers</a>
<a href='../links.php'>Links</a>
<a href='../posts.php'>Posts</a>
<a href='../blogs.php'>Blogs</a>
<a href='../search.php'>Search</a>
<a href='../stats.php'>Zeitgeist</a>
<a class='tab_selected' href='doku.php'>Wiki</a>
</div>