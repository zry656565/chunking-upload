<?php

file_put_contents('/Users/Jerry/Dev/uploads/log', $current, LOCK_EX);
function log($str) {
	file_put_contents('/Users/Jerry/Dev/uploads/log', $str, FILE_APPEND | LOCK_EX);
}
log('[[$_FILES]]:' . print_r($_FILES, true));
log('[[$_POST]]:' . print_r($_POST, true));

if ($_POST['total'] === '1') {
	$uploaddir = '/Users/Jerry/Dev/uploads/';
	$uploadfile = $uploaddir . basename($_POST['name']);

	if (move_uploaded_file($_FILES['fileData']['tmp_name'], $uploadfile)) {
		log('success\n');
	} else {
		log('failure\n');
	}
}