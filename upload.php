<?php

function print_log($str) {
	file_put_contents('/Users/Jerry/Dev/uploads/log', $str, FILE_APPEND | LOCK_EX);
}

if ($_POST['total'] === '1') {
	$uploaddir = '/Users/Jerry/Dev/uploads/';
	$uploadfile = $uploaddir . basename($_POST['name']);

	if (move_uploaded_file($_FILES['fileData']['tmp_name'], $uploadfile)) {
		print_log($_POST['name'] . ' upload success\n');
	} else {
		print_log($_POST['name'] . ' upload failure\n');
	}
} else { //chunk uploading
	$uploaddir = '/Users/Jerry/Dev/uploads/';
	$uploadfile = $uploaddir . basename($_POST['name']);

	if (file_put_contents($uploadfile . '_' . $_POST['index'], $_POST['fileData'], FILE_APPEND | LOCK_EX)) {
		print_log($_POST['name'] . ' upload success\n');
	} else {
		print_log($_POST['name'] . ' upload failure\n');
	}
}