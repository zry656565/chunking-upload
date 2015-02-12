/**
 * @author: Jerry Zou
 * @email: jerry.zry@outlook.com
 */

//= require jquery

(function($) {
    $.jUploader = function(options) {
        options = $.extend({
            url: '',
            buttonSelector: null,
            logSelector: null,
            fileSelector: null,
            singleSize: 4 * 1024 * 1024,    //default: 4MB
            chunkSize: 4 * 1024 * 1024,     //default: 4MB
            afterSuccess: function(total) {},
            chunkSuccess: function(index, completeNum, total) {}
        }, options);

        function log(content) {
            if (options.logSelector) {
                $(options.logSelector).append(content + '<br/>');
            } else {
                console.log(content);
            }
        }

        $(options.buttonSelector).click(function() {
            var file = $(options.fileSelector)[0].files[0],
                name = file.name,
                size = file.size,
                succeed = 0,
                chunkSize = options.chunkSize,
                chunkNum = Math.ceil(size / chunkSize),
                begin = Date.now();

            log('Upload Begin: ' + new Date(begin).toLocaleTimeString());

            // if size of the file is no more than critical size
            if (size <= options.singleSize) {
                var form = new FormData();
                form.append("fileData", file);
                form.append("name", name);
                form.append("total", 1);
                form.append("index", 0);

                $.ajax({
                    url: options.url,
                    type: 'post',
                    data: form,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        log(name + ' begin sending!');
                    },
                    success: function () {
                        log('Upload End: ' + new Date(Date.now()).toLocaleTimeString());
                        log('Takes: ' + (Date.now() - begin) + 'ms');
                        options.afterSuccess(1);
                    }
                });
                return;
            }

            //chunk upload
            for (var i = 0; i < chunkNum; i++) {
                (function(i) {
                    var reader = new FileReader(),
                        start = i * chunkSize,
                        end = Math.min(size, start + chunkSize);

                    reader.onloadend = function () {
                        $.ajax({
                            url: options.url,
                            type: 'post',
                            data: {
                                fileData: reader.result,
                                name: name,
                                total: chunkNum,
                                index: i
                            },
                            beforeSend: function() {
                                log('[index:' + i + '] begin sending!');
                            },
                            success: function () {
                                options.chunkSuccess(i, ++succeed, chunkNum);
                                if (succeed === chunkNum) {
                                    log('Upload End: ' + new Date(Date.now()).toLocaleTimeString());
                                    log('Takes: ' + (Date.now() - begin) + 'ms');
                                    options.afterSuccess(chunkNum);
                                }
                            }
                        });
                    };
                    reader.readAsDataURL(file.slice(start, end));
                }(i));

            }
        });
    };
}(jQuery));