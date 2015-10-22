<?php

//$DEBUG=true;

include_once "/opt/fpp/www/common.php";
include_once 'functions.inc.php';
include_once 'commonFunctions.inc.php';

$myPid = getmypid();

$gitURL = "https://github.com/LightsOnHudson/FPP-Plugin-Tweeter.git";

$pluginName = "Tweeter";
$pluginUpdateFile = $settings['pluginDirectory']."/".$pluginName."/"."pluginUpdate.inc";


$logFile = $settings['logDirectory']."/".$pluginName.".log";
logEntry("plugin update file: ".$pluginUpdateFile);

if(isset($_POST['updatePlugin']))
{
	$updateResult = updatePluginFromGitHub($gitURL, $branch="master", $pluginName);

	echo $updateResult."<br/> \n";
}

if(isset($_POST['submit']))
{
	
//	echo "Writring config fie <br/> \n";
	
	
	WriteSettingToFile("ENABLED",urlencode($_POST["ENABLED"]),$pluginName);
	WriteSettingToFile("SEPARATOR",urlencode($_POST["SEPARATOR"]),$pluginName);
	WriteSettingToFile("USER",urlencode($_POST["USER"]),$pluginName);
	WriteSettingToFile("oauth_access_token",urlencode($_POST["oauth_access_token"]),$pluginName);
	WriteSettingToFile("oauth_access_token_secret",urlencode($_POST["oauth_access_token_secret"]),$pluginName);
	WriteSettingToFile("consumer_key",urlencode($_POST["consumer_key"]),$pluginName);
	WriteSettingToFile("consumer_secret",urlencode($_POST["consumer_secret"]),$pluginName);
	WriteSettingToFile("TWITTER_LAST",$_POST["TWITTER_LAST"],$pluginName);
	
	if(isset($_POST["RESET_LAST_READ"])) {
		WriteSettingToFile("LAST_READ","0",$pluginName);
	} else {
		WriteSettingToFile("LAST_READ",urlencode($_POST["LAST_READ"]),$pluginName);
	}

}

	
	//$ENABLED = urldecode(ReadSettingFromFile("ENABLED",$pluginName));
	$ENABLED = $pluginSettings['ENABLED'];
	
	//$SEPARATOR = urldecode(ReadSettingFromFile("SEPARATOR",$pluginName));
	$SEPARATOR = $pluginSettings['SEPARATOR'];
	
	//$USER = urldecode(ReadSettingFromFile("USER",$pluginName));
	$USER = $pluginSettings['USER'];
	
	//$OAUTH_ACCESS_TOKEN = urldecode(ReadSettingFromFile("oauth_access_token",$pluginName));
	$OAUTH_ACCESS_TOKEN = $pluginSettings['oauth_access_token'];
	
	//$OAUTH_ACCESS_TOKEN_SECRET = urldecode(ReadSettingFromFile("oauth_access_token_secret",$pluginName));
	$OAUTH_ACCESS_TOKEN_SECRET = $pluginSettings['oauth_access_token_secret'];
	
	//$CONSUMER_KEY = urldecode(ReadSettingFromFile("consumer_key",$pluginName));
	$CONSUMER_KEY = $pluginSettings['consumer_key'];
	
	//$CONSUMER_SECRET = urldecode(ReadSettingFromFile("consumer_secret",$pluginName));
	$CONSUMER_SECRET = $pluginSettings['consumer_secret'];
	
	
	//$TWITTER_LAST_INDEX = ReadSettingFromFile("TWITTER_LAST",$pluginName);
	$TWITTER_LAST_INDEX = $pluginSettings['TWITTER_LAST'];
	
	
	//$LAST_READ = urldecode(ReadSettingFromFile("LAST_READ",$pluginName));
	$LAST_READ = $pluginSettings['LAST_READ'];
	
	if($SEPARATOR == "") {
		$SEPARATOR="|";
	}
	//echo "sports read: ".$SPORTS."<br/> \n";
	
	if((int)$LAST_READ == 0 || $LAST_READ == "") {
		$LAST_READ=0;
	}
	
	if((int)$TWITTER_LAST_INDEX == 0 || $TWITTER_LAST_INDEX == "") {
		$TWITTER_LAST_INDEX=0;
	}
?>

<html>
<head>
</head>

<div id="<?echo $pluginName;?>" class="settings">
<fieldset>
<legend><?php echo $pluginName;?> Support Instructions</legend>

<p>Known Issues:
<ul>
<li>NONE</li>
</ul>

<p>Configuration:
<ul>
<li>Configure your username: Tokens, etc</li>
</ul>
<ul>
<li>You must enable APP support READ & WRITE on Twitter.com</li>
<li>You will need to re-generate tokens for every change you make to your app authorization</li>
<li>Copy those authorizations to this plugin and SAVE the config</li>
</ul>



<form method="post" action="http://<? echo $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']?>/plugin.php?plugin=<?echo $pluginName;?>&page=plugin_setup.php">


<?
echo "<input type=\"hidden\" name=\"LAST_READ\" value=\"".$LAST_READ."\"> \n";
echo "<input type=\"hidden\" name=\"TWITTER_LAST\" value=\"".$TWITTER_LAST_INDEX."\"> \n";


$restart=0;
$reboot=0;

echo "ENABLE PLUGIN: ";

if($ENABLED== 1 || $ENABLED == "on") {
		echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
//PrintSettingCheckbox("Radio Station", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
	} else {
		echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
}

echo "<p/> \n";



	echo "<p/> \n";
	
	echo "Twitter Username: \n";
	
	echo "<input type=\"text\" name=\"USER\" size=\"16\" value=\"".$USER."\"> \n";
	
	echo "<p/> \n";
	
	echo "OAUTH Access Token: \n";
	
	echo "<input type=\"text\" name=\"oauth_access_token\" size=\"64\" value=\"".$OAUTH_ACCESS_TOKEN."\"> \n";
	
	
	echo "<p/> \n";
	
	echo "OAUTH Access Token Secret: \n";
	
	echo "<input type=\"text\" name=\"oauth_access_token_secret\" size=\"64\" value=\"".$OAUTH_ACCESS_TOKEN_SECRET."\"> \n";
	
	
	echo "<p/> \n";
	
	echo "Consumer Key: \n";
	
	echo "<input type=\"text\" name=\"consumer_key\" size=\"64\" value=\"".$CONSUMER_KEY."\"> \n";
	echo "<p/> \n";
	
	echo "Consumer Secret: \n";
	
	echo "<input type=\"text\" name=\"consumer_secret\" size=\"64\" value=\"".$CONSUMER_SECRET."\"> \n";
	
	

?>
<p/>
<input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">

<?
 if(file_exists($pluginUpdateFile))
 {
 	//echo "updating plugin included";
	include $pluginUpdateFile;
}
?>
</form>

<p>To report a bug, please file it against the sms Control plugin project on Git: https://github.com/LightsOnHudson/FPP-Plugin-Twitter

</fieldset>
</div>
<br />
</html>
