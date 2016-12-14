<?php

// Jenkins build board status report
// Author Ben Janos (bjanos@gmail.com)
//
// This query your jenkins job statuses and generate a text file (status.txt) 
// that has all 60 LED colors (representing build state) for the showBuild.py to consume.
// See showBuild.py for an example of the .txt file
// 
// It uses the httpful PHP library to query Jenkins
// Get it here - https://github.com/nategood/httpful
// or here - http://phphttpclient.com/

// Set to the path of the httpful library you installed
include('/home/pi/BuildLight/httpful.phar');

// Set your 10 jenkins build jobs here.
// Up to 10 jobs supported.  Shown on board as 0-4 on top row and 5-9 on bottom row

$uris = array();
// Top Row (left to right)
$uris[0] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
$uris[1] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
$uris[2] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
$uris[3] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
$uris[4] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
// Bottom Row (left to right)
$uris[5] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
$uris[6] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
$uris[7] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
$uris[8] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";
$uris[9] = "http://MY_HOST_HERE/jenkins/job/MY_JOB_NAME_HERE/api/json";

// Path where output file is put
$file = "/home/pi/BuildLight/status.txt";
file_put_contents($file, "");

for ($i = 0; $i < 5; $i++) {
	$response = \Httpful\Request::get($uris[$i])->authenticateWith('USER_NAME', 'PASSWORD')->send();
	$buildList = $response->body->builds;
	$buildHistory = "";
	$buildHistoryCount = count($buildList);
	if($buildHistoryCount > 6) {
		// Only show the most recent 6 builds
		$buildHistoryCount = 6;
	}
	for ($x = 0; $x < 6; $x++) {
		$isFirst = false;
		if($x == 0) {
			$isFirst = true;
		}
		if($x >= $buildHistoryCount) {
			$buildHistory = setColor('OFF', $buildHistory, $isFirst);
		} else {
			$buildUrl = $buildList[$x]->url . "api/json";
			$response = \Httpful\Request::get($buildUrl)->authenticateWith('USER_NAME', 'PASSWORD')->send();
			$building = $response->body->building;
			if(!$building){
				$result = $response->body->result;
				$buildHistory = setColor($result, $buildHistory, $isFirst);
			} else {
				$buildHistory = 'yellow yellow yellow yellow yellow yellow';
				break;
			}
		}
	}
	file_put_contents($file, $buildHistory . "\n", FILE_APPEND | LOCK_EX);
}	
	
for ($i = 9; $i > 4; $i--) {
	// We need to reverse the order of the colors because my lights are upside down on the bottom row
	// LEDs on bottom row go from 59 - 30 (left to right)
	$response = \Httpful\Request::get($uris[$i])->authenticateWith('USER_NAME', 'PASSWORD')->send();
	$buildList = $response->body->builds;
	$buildHistory = "";
	$buildHistoryCount = count($buildList);
	if($buildHistoryCount > 6) {
		// Only show the most recent 6 builds
		$buildHistoryCount = 6;
	}
	$buildHistoryCount = $buildHistoryCount - 1;
	
	for ($x = 5; $x >= 0; $x--) {
		$isFirst = false;
		if($x == 5) {
			$isFirst = true;
		}
		if($x > $buildHistoryCount) {
			$buildHistory = setColor('OFF', $buildHistory, $isFirst);
		} else {
			$buildUrl = $buildList[$x]->url . "api/json";
			$response = \Httpful\Request::get($buildUrl)->authenticateWith('USER_NAME', 'PASSWORD')->send();
			$building = $response->body->building;
			if(!$building){
				$result = $response->body->result;
				$buildHistory = setColor($result, $buildHistory, $isFirst);
			} else {
				$buildHistory = 'yellow yellow yellow yellow yellow yellow';
				break;
			}
		}
		print 'LINE = ' . $buildHistory;
	}
	file_put_contents($file, $buildHistory . "\n", FILE_APPEND | LOCK_EX);
}

// Path to your showBuild.py file
// This is what will actually set your LED colors and turn them on or off
$command = escapeshellcmd('sudo /usr/bin/python /home/pi/BuildLight/showBuild.py');
$output = shell_exec($command);

function setColor($result, $buildHistory, $isFirst) {
	if($result === 'SUCCESS') {
		if($isFirst) {
			$buildHistory = "green";
		} else {
			$buildHistory = $buildHistory . " green";
		}
	} elseif($result === 'FAILURE') {
		if($isFirst) {
			$buildHistory = "red";
		} else {
			$buildHistory = $buildHistory . " red";
		}
	} elseif($result === 'ABORTED') {
		if($isFirst) {
			$buildHistory = "blue";
		} else {
			$buildHistory = $buildHistory . " blue";
		}
	} elseif($result === 'OFF') {
		print 'SET COLOR OFF';
		if($isFirst) {
			$buildHistory = "off";
		} else {
			$buildHistory = $buildHistory . " off";
		}
	}
	return $buildHistory;
}
?>
