<?
include("./mp3lib.php");
include("./config.php");

//Extract 30 seconds starting after 10 seconds. 



$stuckgrosse = $_POST['partlenght'];
if (!$stuckgrosse) $stuckgrosse = 10;

$filename = $_POST['filename'];


//teste ob dateiname bereits exestiert
if (dir($archive_path."/".date("Y-m-d-H-i").$filename))
	{
	//wenn es exestiert hochzahlen
	$filenamecounter = 2;
	while (dir($archive_path."/".date("Y-m-d-H-i").$filename."(".$filenamecounter.")"))
		{
		$filenamecounter++;
		}
	//wenn verzeichniss nicht, dann ist verzeichniss hochgezahlt
	$current_file_dir = date("Y-m-d-H-i").$filename."(".$filenamecounter.")";
	}
else $current_file_dir = date("Y-m-d-H-i").$filename;

mkdir($archive_path."/".$current_file_dir);

if ($_FILES['file']) echo("da is was: ".$_FILES['file']['error']."<br>");

if(move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir."/".$current_file_dir.".mp3")) {
    echo "Upload hat funktioniert";
} else{
    echo "There was an error uploading the file, please try again!";
    rmdir($archive_path."/".$current_file_dir);
    die();
}










$path = $upload_dir.'/'.$current_file_dir.".mp3"; 
$mp3 = new mp3($path); 



//m3u8 header
$m3u8_file = "#EXTM3U 
#EXT-X-MEDIA-SEQUENCE:0 
#EXT-X-TARGETDURATION:".$stuckgrosse."
";





$zahler = 0;
$position = 0;
$weiter = 1;
while($weiter==1)
{
	
		$mp3_1 = $mp3->extract($position,$stuckgrosse); 
		$mp3_1->save($archive_path."/".$current_file_dir.'/'.'file'.$position.'.mp3'); 
		
		
		
		//Prüfen ob titel zu ende
		if (filesize($archive_path."/".$current_file_dir.'/'.'file'.$position.'.mp3')<2)
		{
			$weiter = 0;
			unlink($archive_path."/".$current_file_dir.'/'.'file'.$position.'.mp3');
		}
		
		
		
		//File schreiben
		else {
			$m3u8_file = $m3u8_file."#EXTINF:".$stuckgrosse.", 
http://".$extern_archive_path."/".$current_file_dir.'/'.'file'.$position.'.mp3
';}
		
		
		$position = $position + $stuckgrosse;
}



$m3u8_file = $m3u8_file."#EXT-X-ENDLIST";

//m3u8 file schreiben
$dz = fopen($archive_path."/".$current_file_dir.'/'.'index.m3u8',w);
fwrite($dz,$m3u8_file);
fclose($dz);

echo("Upload completet: <a href=\"http://".$extern_archive_path."/".$current_file_dir.'/'.'index.m3u8'."\">Stream URL</a>");
unlink($upload_dir."/".$current_file_dir.".mp3");

?>