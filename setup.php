<?
include_once('functions.php');
//check for #user file. no userfile = Setup
if (filesize("#user.php")>1)
{
	include("#auth.php");
}


include("./config.php");
include("./#user.php");





if ($_POST['Button_save'] == "Save")
{
	$save = new SaveConfig;
	$save->srightusername=$_POST['newusername'];
	$save->srightpassword=$_POST['newpassword'];
	$save->srightpassword2=$_POST['newpassword2'];
	$save->sexternarchivepath=$_POST['externarchivepath'];
	$save->sarchive_path=$_POST['archivepath'];
	$save->supload_dir=$_POST['uploadpath'];
	$save->sdefault_mp3_part_length=$_POST['defaultlength'];
	
	$save->save();
	
	
}









include("./config.php");


//fill in existing Userdata
if (filesize("./#user.php")>1)
{
	include("./#user.php");
	$rightusername = decodeuserdata($rightusername);
	$externarchivepath = $extern_archive_path;
	$archive_path = $archive_path;
	$upload_dir = $upload_dir;
	$default_mp3_part_length = $default_mp3_part_length;
}
//OR generate new Settings on new installation OR Passwordreset
else
{
	$currentpath = 'http://' . $_SERVER['HTTP_HOST'] . str_replace("setup.php","",$_SERVER['SCRIPT_NAME']);
	$externarchivepath = $currentpath."archive";
	
	$archive_path = "archive";
	$upload_dir = "upload";
	$default_mp3_part_length = "10";
}














class SaveConfig
{
	public $srightusername;
	public $srightpassword;
	public $srightpassword2;
	public $sexternarchivepath;
	public $sarchive_path;
	public $supload_dir;
	public $sdefault_mp3_part_length;
	
	private $errormessage;
	private $successmessage;
	
	function checkUserChanges()
	{
		//Username and both passwords are given
		if(($this->srightusername)&&($this->srightpassword)&&($this->srightpassword2))
		{
			//Passwords are not identic
			if (($this->srightpassword)!=($this->srightpassword2))
			{
				$this->addErrorMessage("Passwords not identical");
				return false;
			}
			//Username given, both passwords didentic -> all right
			else
			{
				return true;
			}
		}
		//Passwords given but no username
		elseif ($this->srightusername == "")
		{
			$this->addErrorMessage("No Username");
			return false;
		}
		//don't use Data to save
		else
		{
			return false;
		}
		
		
	}
	
	//Save User Chaneges as fliped Base64 in #user.php 
	function saveUserChanges()
	{
		
		//decode
		$this->usernameToSave = $this->encodeUserData($this->srightusername);
		$this->passwordToSave = $this->encodeUserData($this->srightpassword);
		
		$data_to_write = "<?
\$rightusername = \"".$this->usernameToSave."\";
\$rightpassword = \"".$this->passwordToSave."\";
?>";
		//write file
		$dz = fopen('#user.php',w);
		fwrite($dz,$data_to_write);
		fclose($dz);
		
	}
	
	//encode user data (fliped Base64)
	function encodeUserData($string)
	{
		$string = base64_encode($string);
		$string = strrev($string);
		return($string);
	}
	
	function addErrorMessage($message)
	{
		$this->errormessage = $this->errormessage."<li>".$message."</li>";
	}
	
	function returnErrorMessage()
	{
		if($this->errormessage)
		{
			return "<h2>Error</h2><ul>".$this->errormessage."</ul>";
		}
	}
	
		function addSuccessMessage($message)
	{
		$this->successmessage = $this->successmessage."<li>".$message."</li>";
	}
	
	function returnSuccessMessage()
	{
		if($this->successmessage)
		{
			return "<h2>Success</h2><ul>".$this->successmessage."</ul>";
		}
	}
	
	
	
	//checks settings configuration
	function checkConfig()
	{
		$this->checkDir($this->sarchive_path);
		$this->checkDir($this->supload_dir);
		//Check MP3 lenth is set
		if(round($this->sdefault_mp3_part_length==0))
		{
			$this->addErrorMessage("MP3 lenth is not a Number");
		}
		elseif(!$this->sexternarchivepath)
		{
			$this->addErrorMessage("Extern Archive URL not set");
		}
		else
		{
		return true;
		}
		
	}
	
	//checks directorys for write access and existence
	function checkDir($dir)
	{
		if (is_writeable($dir))
		{
			return(true);
		}
		else
		{
			$this->addErrorMessage("no write Access to Dir: ".$dir);
			return(false);
		}
	}
	
	function save()
	{
		if ($this->checkUserChanges())
		{
			//check for write access for #user.php 
			if(is_writeable('#user.php'))
			{
				$this->SaveUserChanges();
				$this->addSuccessMessage("Userdata Saved");
			}
			else
			{
				$this->addErrorMessage("no write Access to #user.php");
			}
		}
		
		//Ceck Config Save
		if ($this->checkConfig())
		{
			if(is_writeable('config.php'))
			{
				$this->SaveServerChanges();
				$this->addSuccessMessage("Config Saved");
			}
			else
			{
				$this->addErrorMessage("no write Access to config.php");
			}
		}
	} 
	
	//save Configdata
	function SaveServerChanges()
	{
		$data_to_write = "<?
/*CONFIG FILE*/
/*SERVER CONFIG*/
\$archive_path = \"".$this->sarchive_path."\"; //Path to Archive Dir
\$extern_archive_path = \"".$this->sexternarchivepath."\";
\$upload_dir = \"".$this->supload_dir."\";
/*MP3 SPLIT OPTIONS*/
\$default_mp3_part_length = \"".round($this->sdefault_mp3_part_length)."\"; //default length of MP3 parts in seconds
?>";
		//write file
		$dz = fopen('config.php',w);
		fwrite($dz,$data_to_write);
		fclose($dz);
	}

}


























?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<style type="text/css">

	#formbody {
		position: relative;
		left: 50%;
		margin-top: 20px;
		margin-bottom: 20px;
		padding: 10px;
		border-color: #00ffc5;
		border-style: solid;
		border-width: 1px;
		background-color: #ddfff2;
		margin-left: -175px;
		width: 350px;
		-webkit-border-radius: 10px;
		-moz-border-radius: 10px;
		-webkit-box-shadow: 0px 2px 8px #666666;
    	-moz-box-shadow: 0px 2px 8px #666666;
		box-shadow: 0px 2px 8px #666666;
	}
	
		#error {
		position: relative;
		left: 50%;
		margin-top: 20px;
		margin-bottom: 40px;
		padding: 10px;
		border-color: #ff0c00;
		border-style: solid;
		border-width: 1px;
		background-color: #ffd4c8;
		margin-left: -175px;
		width: 350px;
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
		<title>Setup HTTP-Stream</title>
	</head>
	<body>
		<?
		if ($save)
		{
			if($save->returnErrorMessage())
			{
				echo "<div id=\"error\">".$save->returnErrorMessage()."</div>";
			}
			
			if($save->returnSuccessMessage())
			{
				echo "<div id=\"formbody\">".$save->returnSuccessMessage()."<br><a href=\"./setup.php\">back</a></div>";
				die("</body></html>");
			}
		}
		?>
		<form method="post" action="setup.php" id="formbody">
			<h1>Setup</h1>
			<ul>
				<li>File #user.php need write access</li>
				<li>File config.php need write access</li>
				<li>Dir archive need write access</li>
				<li>Dir upload need write access</li>
			</ul>
			<h2>Usersettings</h2>
			<table>
				<tr><td>New Username: </td><td> <input type="text" name="newusername" value="<?=$rightusername;?>"/></td></tr>
				<tr><td>New Password: </td><td> <input type="password" name="newpassword"/></td></tr>
				<tr><td>New Password: </td><td> <input type="password" name="newpassword2"/></td></tr>
			</table>
			<hr>
			<h2>Serversettings</h2>
			<table>
				<tr><td>Extern archive path: </td><td> <input type="text" name="externarchivepath" value="<?=$externarchivepath;?>"/></td></tr>
				<tr><td>archive path: </td><td> <input type="text" name="archivepath" value="<?=$archive_path;?>"/></td></tr>
				<tr><td>upload path: </td><td> <input type="text" name="uploadpath" value="<?=$upload_dir;?>"/></td></tr>
			</table>
			<hr>
			<h2>Audiosettings</h2>
			<table>
				<tr><td>Default part length (in seconds): </td><td> <input type="text" name="defaultlength" value="<?=$default_mp3_part_length;?>" size="3"/></td></tr>
			</table>
			<hr />
			<input type="submit" value="Save" name="Button_save" />
		</form>
	</body>
</html>
