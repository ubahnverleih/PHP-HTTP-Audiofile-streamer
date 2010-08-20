<?
include("./config.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Upload to HTTP-Stream</title>
	</head>
	<body>
		<form method="post" action="split.php" enctype="multipart/form-data">
			<table>
				<tr><td>Name:</td><td> <input type="text" name="filename"/></td></tr>
				<tr><td>Teilstücklänge: </td><td> <input type="text" name="partlenght" value="<?=$default_mp3_part_length;?>" size="3" />s</td></tr>
				<tr><td>Datei </td><td> <input type="file" name="file" accept="audio/x-mpeg" maxlength="16000000"></td></tr>
				<tr><td><input value="Upload" type="submit"></td><td></td></tr>
			</table>
		</form>
	</body>
</html>
