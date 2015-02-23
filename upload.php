<?php

define('UPLOAD_DIR', '/Users/Jerry/Dev/uploads/');

function print_log($str) {
	$str = date('Y-M-d,H:i:s') . ': ' . $str;
	file_put_contents(UPLOAD_DIR.'log', $str, FILE_APPEND | LOCK_EX);
}

if ($_POST['clean'] == true) {
	$uploaddir = UPLOAD_DIR;
	$uploadfile = $uploaddir . basename($_POST['name']);

	$files = scandir($uploaddir);
	$existChunk = 0;
	foreach ($files as $file) {
		if (substr($file, 0, strlen($_POST['name'])) === $_POST['name']) {
			unlink(UPLOAD_DIR . $file);
			$existChunk++;
		}
	}

	if ($existChunk) {
		die('{"OK": 1, "info": "Clean '.$existChunk.' chunks/files with name (' .basename($_POST['name']). ') on server"}');
	} else {
		die('{"OK": 1, "info": "There is no chunks/files with name (' .basename($_POST['name']). ') on server"}');
	}
}

if ($_POST['name'] == '') {
	print_log("uploading abort.\n");
	die('{"OK": 0, "info": "Uploading abort."}');
} else if ($_POST['total'] == 1) {
	$uploaddir = UPLOAD_DIR;
	$uploadfile = $uploaddir . basename($_POST['name']);

	//if exists, delete it first
	if(file_exists($uploadfile)) {
		unlink($uploadfile);
	}

	if (move_uploaded_file($_FILES['fileData']['tmp_name'], $uploadfile)) {
		print_log($_POST['name'] . " upload success\n");
		die('{"OK": 1, "info": "'. $_POST['name'] . " upload success" .'"}');
	} else {
		print_log($_POST['name'] . " upload failure\n");
		die('{"OK": 0, "info": "Failed to move uploaded file."}');
	}
} else { // chunk uploading
	$uploaddir = UPLOAD_DIR;
	$uploadfile = $uploaddir . basename($_POST['name']);

	$buffer = explode(",", $_POST['fileData']);
	$buffer = $buffer[1];
	$data = base64_decode($buffer);
	file_put_contents($uploadfile . '_' . $_POST['index'], $data, LOCK_EX);
	print_log($_POST['name'] . ' - ' . $_POST['index'] . " arrived.\n");

	// combine those chunks
	$files = scandir($uploaddir);
	$receivedChunkNum = 0;
	foreach ($files as $file) {
		if (substr($file, 0, strlen($_POST['name'])) === $_POST['name']) {
			$receivedChunkNum++;
		}
	}
	if ($receivedChunkNum == $_POST['total']) {
		if(file_exists($uploadfile)) {
			unlink($uploadfile);
		}
		for ($i = 0; $i < $receivedChunkNum; $i++) {
			$buffer = file_get_contents($uploadfile . '_' . $i);
			file_put_contents($uploadfile, $buffer, FILE_APPEND | LOCK_EX);
			unlink($uploadfile . '_' . $i);
		}
		print_log($_POST['name'] . " upload success\n");
		die('{"OK": 1, "info": "'. $_POST['name'] . " upload success" .'"}');
	} else {
		die('{"OK": 1, "info": "'. $_POST['name'] . ' - ' . $_POST['index'] . " arrived." .'"}');
	}
}