<?
//functions
//decode Userdata
function decodeuserdata($string)
{
	$string = strrev($string);
	$string = base64_decode($string);
	return($string);
}
?>