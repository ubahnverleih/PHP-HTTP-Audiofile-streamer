<?
include_once('functions.php');
$username = $_POST['username'];
$password = $_POST['password'];

//check for #user file. no userfile = redirect to setup
if (filesize("#user.php")<1)
{
	header("Location: ./setup.php");
	die();
}

//authorise and set session and redirect
if (($username)&&($password))
{
	include("#user.php");
	//decode password
	$rightusername = decodeuserdata($rightusername);
	$rightpassword = decodeuserdata($rightpassword);
	
	if(($username==$rightusername)&&($password==$rightpassword))
	{
		session_start();
		$_SESSION['auth'] = 1;
		header("Location: ./upload.php");
		die();
	}
}

//check session and redirect
session_start();
if($_SESSION['auth'] == 1)
{
	header("Location: ./upload.php");
	die();
}









?>
<html>
	<head>
	<style type="text/css">

	#formbody {
		position: absolute;
		left: 50%;
		top: 50%;
		margin-top: -75px;
		padding: 10px;
		border-color: #00ffc5;
		border-style: solid;
		border-width: 1px;
		background-color: #ddfff2;
		margin-left: -100px;
		width: 200px;
		-webkit-border-radius: 10px;
		-moz-border-radius: 10px;
		-webkit-box-shadow: 0px 2px 8px #666666;
    	-moz-box-shadow: 0px 2px 8px #666666;
		box-shadow: 0px 2px 8px #666666;
	}

	body {
		background-color: #f6fff8;
	}

	</style>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Upload to HTTP-Stream - Login</title>
	</head>
	<body>
		<form method="post" action="index.php" id="formbody">
			<table>
				<tr><td>Username:</td><td> <input type="text" name="username" /></td></tr>
				<tr><td>Password: </td><td> <input type="password" name="password" /></td></tr>
				<tr><td><input value="Login" type="submit"></td><td></td></tr>
			</table>
		</form>
	</body>
</html>

