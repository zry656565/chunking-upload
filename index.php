<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>文件分片上传</title>
</head>
<body>

<input id="file" type="file"/>
<button id="upload">上传</button>
<span id="log"></span>

<!-- script -->
<script src="assets/js/jquery-1.11.2.min.js"></script>
<script src="assets/js/jUploader.js"></script>
<script>
	$(function(){
		$.jUploader({
			buttonSelector: '#upload',
			fileSelector: '#file',
			chunkSize: 4 * 1024 * 1024, //4MB
			afterSuccess: function(total) { console.log('success'); },
			chunkSuccess: function(i, completeNum, total) {
				$('#log').html(completeNum + '/' + total);
				console.log('[index:' + i + '] arrived.');
			}
		});
	});
</script>

</body>
</html>