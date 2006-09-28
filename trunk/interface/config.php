<?
	$path_to_pipeline = "../";
	$GLOBALS['path_to_pipeline'] = $path_to_pipeline;

	$config_file = file_get_contents($path_to_pipeline."conf/default.conf");

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
