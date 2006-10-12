<?
	$path_to_pipeline = "../";
	$GLOBALS['path_to_pipeline'] = $path_to_pipeline;
	
	# need to get a listing of the .conf files in the conf directory...
	$config_files = glob($path_to_pipeline."conf/*.conf");
	

	$config_file = $path_to_pipeline."conf/default.conf";
	if ($config_files) {
		foreach ($config_files as $current_config_file) {
			if ($current_config_file != $config_file) {
				$config_file = $current_config_file;
			}
		}
	}
	$config_file = file_get_contents($config_file);
		
	global $config;
	$config = array();
	$config_lines = preg_split("/[\n\r]/", $config_file);

	foreach ($config_lines as $config_line) {
		$matches = array();
		if (preg_match("/(.*)=(.*)/", $config_line, $matches)) {
			$config[$matches[1]] = $matches[2];
		}
	}

	# to debug, uncomment the line below
	# foreach ($config as $key => $val) {print "$key ==> $val\n";}
?>
