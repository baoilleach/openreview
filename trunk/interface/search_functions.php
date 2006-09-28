<?
function do_search($term, $safe_skip = 0, $max = false, $safe_type = "any") {
	global $config;
	
	$safe_search = reduce_to_ascii($term, true);
	if (!$max) {$max = $config['search_results_per_page'];} # $max is per page, not the limit of the number of results
	
	$url = $GLOBALS['config']['pylucene_url']."?q=".urlencode($safe_search)."&start=$safe_skip&max=$max&type=$safe_type";
	
	$results = download_url($url);
	
	return $results;
}

function parse_search_results($results) {
	
	$num_results = 0;
	$total_results = 0;
	$limit = 0;
	
	$return;
	
	if ($results) {
		$lines = explode("\n", $results);
		# filter out blank lines
		$filenames = array();
		foreach ($lines as $line) {
			if (strlen($line) >= 3) {array_push($filenames, $line);}
			
			$matches = array();
			preg_match("/===META=TYPE===(\d+)===/", $line, $matches);
			if ($matches[1]) {$num_results = $matches[1];}
			$matches = array();
			preg_match("/===META=TOTAL===(\d+)===/", $line, $matches);
			if ($matches[1]) {$total_results = $matches[1];}
			$matches = array();
			preg_match("/===META=LIMIT===(\d+)===/", $line, $matches);
			if ($matches[1]) {$limit = $matches[1];}
		}
		
		# first get any posts
		$posts = array();
		if (sizeof($filenames)) {
			$posts_files = get_posts_by_filenames($filenames);
			if (sizeof($posts_files)) {
				$post_ids = array_values($posts_files);
				$post_filenames = array_keys($posts_files);
 				$posts = get_posts("published_on", array("post_id" => $post_ids, "order_by_filenames_array" => $filenames));
				# add rows_returned field to first post (used by pagination algorithm)
				$posts[0]["rows_returned"] = $num_results;
			}
		}
		
		# then any blogs
		$blogs = array();
		if (sizeof($filenames)) {
			$blogs_files = get_blogs_by_filenames($filenames);
			if (sizeof($blogs_files)) {
				$blogs = get_blogs($blogs_files);
				$blogs[0]["rows_returned"] = $num_results;
			}
		}
		
		# and any papers...
		$papers = array();
		if (sizeof($filenames)) {
			$paper_ids = array();
			foreach ($filenames as $filename) {
				$matches = array();
				preg_match("/paper_(\d+)\.xml/i", $filename, $matches);
				if ($matches) {
					array_push($paper_ids, $matches[1]);
				}
			}
			if (sizeof($paper_ids)) {
				$papers = get_papers("cited", array("paper_id" => $paper_ids));
				$papers[0]["rows_returned"] = $num_results;
			}
		}	
		
		$return["posts"] = $posts;
		$return["blogs"] = $blogs;
		$return["papers"] = $papers;
		$return["limit"] = $limit;
		$return["total_results"] = $total_results;
		$return["num_results"] = $num_results;
			
	}
	
	return $return;	
}

?>