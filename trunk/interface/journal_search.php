<? include("functions.php"); ?>
<?
	$PAGE_TYPE = "zeitgeist";
	$PAGE_TITLE = "Postgenomic - Journal details";

?>
<? include("header.php"); ?>
<?
$safe_journal_id = mysql_escape_string($_GET['journal_id']);
$last_month = date("Y-m-d", mktime(0,0,0, date(m)-1, date(d),date(Y))); 
	
if ($safe_journal_id) {
?>
<div class='sidebar'>
<?
	$stats = get_journal_stats($safe_journal_id);

	print "<h3>Tags associated with this journal</h3>";
	$tags = get_tags_for_journal($safe_journal_id, 50);
	print_tagcloud($tags);

	if ($stats) {
		print_journal_stats($safe_journal_id, $stats);
	}
?>
</div>
<div class='content'>
<?
	print "<div class='journalbox'>";
		print "<a href='".linkto("papers.php", $page_vars, array("journal" => $safe_journal_id))."'><div class='scorebox'>";
		print "<img border='0' src='images/comments.png'/>";
		print $stats['incoming_links'];
		print "</div></a> ";
	
		print "<div class='journalbox_title'>";
			print "<div class='magnify'>";
			print "$safe_journal_id";
			print "</div>";
		print "</div>";
		
		print "<div class='journalbox_footer'>";
			print "&nbsp;";
		print "</div>";
	
	print "</div>";
?>
<table width='100%' cellspacing='10' cellpadding='0'>
<tr>
<td valign='top' width='50%'>
<?
	print "<h3>All time most popular papers</h3>";
		$papers = get_papers("cited", array("journal" => $safe_journal_id, "limit" => 5));
	foreach ($papers as $paper) {
		print_paper($paper, array("display" => "minimal"));
	}
?>
</td>
<td valign='top' width='50%'>
	<?
		print "<h3>Recent hot papers</h3>";
		$papers = get_papers("cited", array("journal" => $safe_journal_id, "limit" => 5, "published_after" => $last_month));
		foreach ($papers as $paper) {
			print_paper($paper, array("display" => "minimal"));
		}
	?>	
</td>
</tr>
</table>
</div>
<?
} else {
	print_error("No journal specified", "Sorry, I'm not sure which journal you're looking for.");
}
?>
<? include("footer.php");?>