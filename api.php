<?php

$liveDir = './live/';
session_start();

function getRoom() {
	global $liveDir;
	$m3u8 = glob($liveDir . '*.m3u8');
	foreach($m3u8 as &$v) {
		$v = substr($v, strlen($liveDir),-5);
	}
	echo json_encode($m3u8);
}

function getUid() {
	echo json_encode(session_id());
}


if(isset($_GET['getRoom'])) {
	getRoom();
}

if(isset($_GET['getUid'])) {
	getUid();
}
