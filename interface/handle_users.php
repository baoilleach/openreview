<?
	# check for login cookie
	global $logged_on;
	$logged_on = false;
	$is_admin = false;
	
	if ($_COOKIE['pg_logged_on']) {
		$logged_on = mysql_escape_string($_COOKIE['pg_logged_on']);
		if ($logged_on == "Euan") {$is_admin = true;} # yes, it's a horrible hack.
	}
	if ($_GET['logout']) {
		setcookie("pg_logged_on");
		$logged_on = false;
	}
?>