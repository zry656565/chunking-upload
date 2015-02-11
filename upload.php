<?php

file_put_contents('/Users/Jerry/Dev/uploads/log', '', LOCK_EX);
function print_log($str) {
	file_put_contents('/Users/Jerry/Dev/uploads/log', $str, FILE_APPEND | LOCK_EX);
}
print_log('[[$_FILES]]:' . print_r($_FILES, true));
print_log('[[$_POST]]:' . print_r($_POST, true));

if ($_POST['total'] === '1') {
	$uploaddir = '/Users/Jerry/Dev/uploads/';
	$uploadfile = $uploaddir . basename($_POST['name']);

	if (move_uploaded_file($_FILES['fileData']['tmp_name'], $uploadfile)) {
		print_log('success\n');
	} else {
		print_log('failure\n');
	}
}