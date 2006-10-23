<?
	$blurb["frontpage"] = sprintf("
<h1>Welcome to Postgenomic</h1>
<p>Postgenomic collects data from hundreds of science blogs and then does useful and interesting things with it.
<p>With Postgenomic, you can:
<ul>
<li>Find, read and subscribe to new <a href='%s'>science blogs</a>
<li>Find out what scientists are saying about the latest <a href='%s'>books</a> and <a href='%s'>papers</a>
<li>Read <a href='%s'>mini-reviews</a>, <a href='%s'>conference reports</a> or even <a href='%s'>original research</a>	
<li>See the buzz surrounding <a href='%s'>different websites</a>
<li>Browse different subject areas - <a href='%s'>chemistry</a>, <a href='%s'>bioinformatics</a>, <a href='%s'>neuroscience</a>, the <a href='%s'>life sciences</a>... see the 'Explore' options on the top right hand side of the page for more.
</ul>",
linkto("blogs.php", $page_vars),
linkto("papers.php", $page_vars, array("area" => "books")),
linkto("papers.php", $page_vars),
linkto("posts.php", $page_vars, array("tag" => "review")),
linkto("posts.php", $page_vars, array("tag" => "conference")),
linkto("posts.php", $page_vars, array("tag" => "original_research")),
linkto("links.php", $page_vars),
linkto("index.php", $page_vars, array("category" => "Chemistry")),
linkto("index.php", $page_vars, array("category" => "Bioinformatics")),
linkto("index.php", $page_vars, array("category" => "Neuroscience")),
linkto("index.php", $page_vars, array("category" => "Life Sciences"))
);

?>