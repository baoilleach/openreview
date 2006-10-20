<?

function print_template_css($safe_category = false) {
	global $config;
	
	# if we haven't set a custom template dir use the default.
	if (!$config['template_dir']) {$config['template_dir'] = "templates/";}
	
	# is there a template available for this category?
	if ($safe_category) {
		$category_template = $config['template_dir'].strtolower(preg_replace("/[^\w]/i", "", $safe_category))."/";
	} else {
		$category_template = $config['template_dir'];
	}
	# Postgenomic uses several CSS files for different sections of the site...
	# Different categories can have different CSS files.
	
	$css = array("basic", "header", "posts", "papers", "blogs", "links", "search", "zeitgeist");
	
	foreach ($css as $file) {
		$custom_path = $category_template.$file.".css";
		
		# by default use the basic version.
		$use = $config['template_dir'].$file.".css";
		
		if (file_exists($custom_path)) {
			# use the custom version
			$use = $custom_path;
		}
		
		print "\t<link rel='stylesheet' type='text/css' href='$use'/>\n";
	}
}
?>