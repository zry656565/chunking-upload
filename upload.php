<?php

function print_log($str) {
	file_put_contents('/Users/Jerry/Dev/uploads/log', $str, FILE_APPEND | LOCK_EX);
}

if ($_POST['total'] == 1) {
	$uploaddir = '/Users/Jerry/Dev/uploads/';
	$uploadfile = $uploaddir . basename($_POST['name']);

	if (move_uploaded_file($_FILES['fileData']['tmp_name'], $uploadfile)) {
		print_log($_POST['name'] . " upload success\n");
	} else {
		print_log($_POST['name'] . " upload failure\n");
	}
} else { //chunk uploading
	$uploaddir = '/Users/Jerry/Dev/uploads/';
	$uploadfile = $uploaddir . basename($_POST['name']);

	file_put_contents($uploadfile . '_' . $_POST['index'], $_POST['fileData'], FILE_APPEND | LOCK_EX);
	print_log($_POST['name'] . ' - ' . $_POST['index'] . " arrived.\n");

	//combine those chunks
	$files = scandir($uploaddir);
	$receivedChunkNum = 0;
	foreach ($files as $file) {
		if (substr($file, 0, strlen($_POST['name'])) === $_POST['name']) {
			$receivedChunkNum++;
		}
	}
	if ($receivedChunkNum == $_POST['total']) {
		for ($i = 0; $i < $receivedChunkNum; $i++) {
			$data = file_get_contents($uploadfile . '_' . $i);
			file_put_contents($uploadfile, $data, FILE_APPEND | LOCK_EX);
			unlink($uploadfile . '_' . $i);
		}
		print_log($_POST['name'] . " upload success\n");
	}
}