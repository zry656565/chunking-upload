<?php

define('UPLOAD_DIR', '/Users/Jerry/Dev/uploads/');

function print_log($str) {
	file_put_contents(UPLOAD_DIR.'log', $str, FILE_APPEND | LOCK_EX);
}

if ($_POST['total'] == 1) {
	$uploaddir = UPLOAD_DIR;
	$uploadfile = $uploaddir . basename($_POST['name']);

	if (move_uploaded_file($_FILES['fileData']['tmp_name'], $uploadfile)) {
		print_log($_POST['name'] . " upload success\n");
	} else {
		print_log($_POST['name'] . " upload failure\n");
	}
} else { //chunk uploading
	$uploaddir = UPLOAD_DIR;
	$uploadfile = $uploaddir . basename($_POST['name']);

	$buffer = explode(",", $_POST['fileData']);
	$buffer = $buffer[1];
	$data = base64_decode($buffer);
	file_put_contents($uploadfile . '_' . $_POST['index'], $data, FILE_APPEND | LOCK_EX);
	print_log($_POST['name'] . ' - ' . $_POST['index'] . " arrived.\n");

	//combine those chunks
	if(file_exists($uploadfile)) {
		unlink($uploadfile);
	}
	$files = scandir($uploaddir);
	$receivedChunkNum = 0;
	foreach ($files as $file) {
		if (substr($file, 0, strlen($_POST['name'])) === $_POST['name']) {
			$receivedChunkNum++;
		}
	}
	if ($receivedChunkNum == $_POST['total']) {
		for ($i = 0; $i < $receivedChunkNum; $i++) {
			$buffer = file_get_contents($uploadfile . '_' . $i);
			file_put_contents($uploadfile, $buffer, FILE_APPEND | LOCK_EX);
			unlink($uploadfile . '_' . $i);
		}
		print_log($_POST['name'] . " upload success\n");
	}
}