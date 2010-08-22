<?
session_start();
$_SESSION['auth'] = 0;
header("Location: ./index.php");
?>