<?
	include_once("config.php");	

	mysql_connect($config['db_host'], $config['db_user'], $config['db_password']);
	
	@mysql_select_db($config['db_name']) or die( "Unable to select database");

?>
