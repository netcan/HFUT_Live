<?php

$liveDir = './live/';
function getRoom() {
	global $liveDir;
	$m3u8 = glob($liveDir . '*.m3u8');
	foreach($m3u8 as &$v) {
		$v = substr($v, strlen($liveDir),-5);
	}
	echo json_encode($m3u8);
}


if(isset($_GET['getRoom'])) {
	getRoom();
}
