<?php

$liveDir = './live/';
// $danmuUrl = 'http://172.18.72.13:2121/';
$danmuUrl = 'http://127.0.0.1:2121/';
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

function send($channel, $content) {
	global $danmuUrl;
	$to_uid = '';
	$post_data = array(
		'type' => 'publish',
		'channel' => $channel,
		'content' => $content,
		'to' => $to_uid,
	);
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $danmuUrl );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );
	var_export($return);
}


if(isset($_GET['getRoom'])) {
	getRoom();
}

if(isset($_GET['getUid'])) {
	getUid();
}

if(isset($_GET['send'])) {
	send($_GET['channel'], $_GET['send']);
}
