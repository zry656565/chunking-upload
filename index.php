<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>文件分片上传</title>
	<style>
		.outer {
			float: left;
			width: 530px;
		}

		#log, #progress {
			border: 1px solid #333;
			padding: 10px;
			width: 500px;
			height: 500px;
			overflow-y: scroll;
			line-height: 1.4;
		}
	</style>
</head>
<body>

<div>
	<input id="file" type="file"/>
	<button id="upload">上传</button>
</div>
<div class="outer">
	<h3>Console.log:</h3>
	<div id="log"></div>
</div>
<div class="outer">
	<h3>Progress:</h3>
	<div id="progress"></div>
</div>

<!-- script -->
<script src="assets/js/jquery-1.11.2.min.js"></script>
<script src="assets/js/jUploader.js"></script>
<script>
	$(function(){
		$.jUploader({
			url: 'upload.php',
			buttonSelector: '#upload',
			logSelector: '#log',
			fileSelector: '#file',
			singleSize: 4 * 1024 * 1024,    //4MB
			chunkSize: 4 * 1024 * 1024, 	//4MB
			parallelRequest: 4,				//并发的请求数
			beforeSend: function() {
				scroll();
			},
			afterSuccess: function(total) {
				$('#log').append('success<br/>');
				scroll();
			},
			chunkSuccess: function(i, completeNum, total) {
				$('#log').append('[index:' + i + '] arrived. ' + completeNum + '/' + total + '<br />');
				$('#progress').find('[index='+ i +']').remove();
				scroll();
			},
			progress: function(i, percentage) {
				var $p = $('#progress');
				if (!$p.find('[index='+ i +']')) {
					$p.append('<p index="' + i + '">[index:' + i + '] - progress:' + percentage.toFixed(1) + '%</p>');
				} else {
					$p.find('[index='+ i +']').html('[index:' + i + '] - progress:' + percentage.toFixed(1) + '%');
				}
			}
		});

		//keep #log scroll to bottom
		function scroll() {
			var log = $('#log')[0];
			log.scrollTop = log.scrollHeight;
		}
	});
</script>

</body>
</html>