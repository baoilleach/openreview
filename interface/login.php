<?
	$safe_username = mysql_escape_string($_POST["username"]);
	$safe_password = mysql_escape_string($_POST["password"]);

	if ($safe_username) {
		setcookie("pg_logged_on", $safe_username);
		header("Location: index.php");
	}
?>
<? include("functions.php"); ?>
<? include("header.php"); ?>
<div class='content fullwidth'>
<h1>Log in or Register</h1>
<p>If you already have a username and password for Postgenomic then enter them here.
<p>If you haven't already registered then enter your desired username and password and then check the "I'm a new user" checkbox.
<div class='loginform'>
<form action="login.php" method="POST">
<table width='100%' cellpadding='0' cellspacing='5'>
<tr><td width='120'>Username:</td><td><input class='textbox' type="text" name="username"/><br/>
</td></tr><tr><td>
Password:</td><td><input class='textbox' type="text" name="password"/>
</td></tr><tr><td colspan='2'>
<input class='textbox' type='checkbox' name="new_user">I'm a new user</input>
</td></tr><tr><td colspan='2'>
<input type='submit' value="Go"/>
</td></tr>
</table>
</form>
</div>
</div>
<? include("footer.php"); ?>