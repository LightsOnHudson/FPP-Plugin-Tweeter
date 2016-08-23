#!/usr/bin/php
<?
error_reporting(0);

$pluginName ="Tweeter";


$skipJSsettings = 1;
include_once("/opt/fpp/www/config.php");

include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");
include_once("commonFunctions.inc.php");


$ENABLED="";

//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/".$pluginName.".log";
//$logFile = $logDirectory."/logs/betabrite.log";
//echo "Enabled: ".$ENABLED."<br/> \n";

$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);


$ENABLED = urldecode($pluginSettings['ENABLED']);


if($ENABLED != "ON" && $ENABLED != "1") {
	logEntry("Plugin Status: DISABLED Please enable in Plugin Setup to use & Restart FPPD Daemon");
	
	exit(0);
}
$callbackRegisters = "media\n";
$myPid = getmypid();
//var_dump($argv);

$FPPD_COMMAND = $argv[1];

//echo "FPPD Command: ".$FPPD_COMMAND."<br/> \n";

if($FPPD_COMMAND == "--list") {

			echo $callbackRegisters;
			logEntry("FPPD List Registration request: responded:". $callbackRegisters);
			exit(0);
}

if($FPPD_COMMAND == "--type") {
		if($DEBUG)
			logEntry("DEBUG: type callback requested");
			//we got a register request message from the daemon
		$forkResult = fork($argv);
		if($DEBUG)
		logEntry("DEBUG: Fork Result: ".$forkResult);
		exit(0); 
		//	processCallback($argv);	
} else {

			logEntry($argv[0]." called with no parameteres");
			exit(0);
}
?>
