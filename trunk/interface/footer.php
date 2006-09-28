</div>
<?
	if ($config['use_urchin']) {
?>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "<? if ($config['urchin_code']) {print $config['urchin_code'];} else {print "UA-54928-6";} ?>";
urchinTracker();
</script>
<?
	}
	
	if ($config['use_crazyegg']) {
?>
<script type="text/javascript">
//<![CDATA[
  document.write('<scr'+'ipt src="http://crazyegg.com/pages/scripts/8322.js?'+(new Date()).getTime()+'" type="text/javascript"></scr'+'ipt>');
//]]>
</script>
<?
	}
?>
<div class='footer'>
(c) 2006 Nature Publishing Group. Copyright for blog posts and author images rests with the authors.
</div>
</body>
</html>
<?
	# if caching was switched on then save the page we just generated.
	if ($PAGE_CACHE) {
		$page = ob_get_contents();
		ob_end_flush(); flush();
		
		# put cached page in database
		cache($PAGE_URL, $page);
	}
?>