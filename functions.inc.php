<?php

//tweet function
function tweet($messageToSend) {
	
	//require_once '/opt/fpp/www/common.php';
	require_once 'TwitterAPIExchange.php';
	//require_once 'twitter.conf.php';
	//include_once("functions.inc.php");
	//include_once("commonFunctions.inc.php");
	
	global $DEBUG, $pluginName, $settings,$pluginSettings;

//logEntry("inside tweet");		
	
	//	$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
	//	if (file_exists($pluginConfigFile))
	//		$pluginSettings = parse_ini_file($pluginConfigFile);
	
		//	$logFile = $settings['logDirectory']."/".$pluginName.".log";
	
			/*
			 * This is a bit hacky. We know that the JSON payload is argument 4 provided
			 * by FPP, and this is what we are really after for our Tweets.
			 * We decode it into an associative array for later use.
			 */
	
			$twitterSettings = array(
					'oauth_access_token' => $pluginSettings['oauth_access_token'], //, 'tweeter'),
					'oauth_access_token_secret' => $pluginSettings['oauth_access_token_secret'],
					'consumer_key' => $pluginSettings['consumer_key'],
					'consumer_secret' => $pluginSettings['consumer_secret']
			);
			$twitterEndpoint = 'https://api.twitter.com/1.1/';
	
			$message = "Now Playing at ".date('M jS, g:ia',time()).": ".$messageToSend;
			logEntry(" Message:".$message);
	
			$url = $twitterEndpoint.'/statuses/update.json';
			$requestMethod = 'POST';
			$postfields = array('status' => $message);
	
			$twitter = new TwitterAPIExchange($twitterSettings);
			//echo $twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest();
			$tweetResult = $twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest();
			logEntry("TweetResult: ".$tweetResult);
}

function hex_dump($data, $newline="\n")
{
  static $from = '';
  static $to = '';

  static $width = 16; # number of bytes per line

  static $pad = '.'; # padding for non-visible characters

  if ($from==='')
  {
    for ($i=0; $i<=0xFF; $i++)
    {
      $from .= chr($i);
      $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
    }
  }

  $hex = str_split(bin2hex($data), $width*2);
  $chars = str_split(strtr($data, $from, $to), $width);

$HEX_OUT ="";
  $offset = 0;
  foreach ($hex as $i => $line)
  {
    $HEX_OUT.= sprintf('%6X',$offset).' : '.implode(' ', str_split($line,2)) . ' [' . $chars[$i] . ']';
    $offset += $width;
  }
return $HEX_OUT;
}

function escapeshellarg_special($file) {
	return "'" . str_replace("'", "'\"'\"'", $file) . "'";
}

//process sequence types

function processSequenceName($sequenceName) {
	logEntry("Sequence name: ".$sequenceName);
	
	$sequenceName = strtoupper($sequenceName);
	
	switch ($sequenceName) {
		
		case "BETABRITE-CLEAR.FSEQ":

		logEntry("Clear BetaBrite Sign");
		$messageToSend="";
		sendLineMessage($messageToSend,$clearMessage=TRUE);
			break;
			exit(0);
			
		default:
			logEntry("We do not support sequence name: ".$sequenceName." at this time");
			
			exit(0);
			
	}
	
}
function processCallback($argv) {

	global $DEBUG,$pluginName, $settings, $pluginSettings;
	
	$SEPARATOR = urldecode($pluginSettings['SEPARATOR']);

	if($DEBUG)
		print_r($argv);
	//argv0 = program

	//argv2 should equal our registration // need to process all the rgistrations we may have, array??
	//argv3 should be --data
	//argv4 should be json data

	$registrationType = $argv[2];
	$data =  $argv[4];

	logEntry("PROCESSING CALLBACK");
	$clearMessage=FALSE;

	switch ($registrationType)
	{
		case "media":
			if($argv[3] == "--data")
			{
				$data=trim($data);
				logEntry("DATA: ".$data);
				$obj = json_decode($data);

				$type = $obj->{'type'};
				
				switch ($type) {
					
					case "sequence":
								
					//$sequenceName = ;
					processSequenceName($obj->{'Sequence'});
					
					break;
					case "media":
					
					logEntry("MEDIA ENTRY: EXTRACTING TITLE AND ARTIST");
					
					$songTitle = $obj->{'title'};
					$songArtist = $obj->{'artist'};
					//	if($songArtist != "") {
				
				
					$messageToSend = $songTitle." ".$SEPARATOR." ".$songArtist;
					logEntry("MESSAGE to send: ".$messageToSend);
					tweet($messageToSend);
					//$tweet_cmd = $settings['pluginDirectory']."/".$pluginName."/tweet.php ".$messageToSend;
					//exec("/usr/bin/php ".$tweet_cmd);
					

				break;
				case "both":
						
					logEntry("MEDIA ENTRY: EXTRACTING TITLE AND ARTIST");
						
					$songTitle = $obj->{'title'};
					$songArtist = $obj->{'artist'};
					//	if($songArtist != "") {
				
				
					$messageToSend = $songTitle." ".$SEPARATOR." ".$songArtist;
					logEntry("MESSAGE to send: ".$messageToSend);
					tweet($messageToSend);
					//$tweet_cmd = $settings['pluginDirectory']."/".$pluginName."/tweet.php ".$messageToSend;
					//exec("/usr/bin/php ".$tweet_cmd);
					
					break;
				
					default:
						logEntry("We do not understand: type: ".$obj->{'type'}. " at this time");
						exit(0);
						break;
						
				}

				
			}
				
			break;
			exit(0);
			
		default:
			exit(0);

	}

}

function logEntry($data) {

	global $logFile,$myPid;

	$data = $_SERVER['PHP_SELF']." : [".$myPid."] ".$data;
	$logWrite= fopen($logFile, "a") or die("Unable to open file!");
	fwrite($logWrite, date('Y-m-d h:i:s A',time()).": ".$data."\n");
	fclose($logWrite);
}




//create script to randmomize
function createRandomizerScript() {


	global $radioStationRepeatScriptFile,$pluginName,$randomizerScript;


	logEntry("Creating Randomizer script: ".$radioStationRepeatScriptFile);

	$data = "";
	$data  = "#!/bin/sh\n";
	$data .= "\n";
	$data .= "#Script to run randomizer\n";
	$data .= "#Created by ".$pluginName."\n";
	$data .= "#\n";
	$data .= "/usr/bin/php ".$randomizerScript."\n";


	$fs = fopen($radioStationRepeatScriptFile,"w");
	fputs($fs, $data);
	fclose($fs);

}

//crate the event file
function createRandomizerEventFile() {

	global $radioStationRepeatScriptFile,$pluginName,$randomizerScript,$radioStationRandomizerEventFile,$MAJOR,$MINOR,$radioStationRadomizerEventName;


	logEntry("Creating Randomizer event file: ".$radioStationRandomizerEventFile);

	$data = "";
	$data .= "majorID=".$MAJOR."\n";
	$data .= "minorID=".$MINOR."\n";

	$data .= "name='".$radioStationRadomizerEventName."'\n";

	$data .= "effect=''\n";
	$data .="startChannel=\n";
	$data .= "script='".pathinfo($radioStationRepeatScriptFile,PATHINFO_BASENAME)."'\n";



	$fs = fopen($radioStationRandomizerEventFile,"w");
	fputs($fs, $data);
	fclose($fs);
}
?>
