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
            parallelRequest: 4,
            beforeSend: function() {},
            afterSuccess: function(total) {},
            chunkSuccess: function(index, completeNum, total) {},
            progress: function() {}
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
                begin;

            //Scan if there is a file with the same name on server
            $.ajax({
                url: options.url,
                type: 'post',
                data: {
                    name: name,
                    clean: true
                },
                beforeSend: function() {
                    log('Scan if there is a file with the same name on server.');
                },
                success: function (response) {
                    log(JSON.parse(response)['info']);

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
                            xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                //Upload progress
                                xhr.upload.addEventListener("progress", function(event){
                                    if (event.lengthComputable) {
                                        options.progress(0, event.loaded / event.total * 100);
                                    }
                                }, false);
                                return xhr;
                            },
                            url: options.url,
                            type: 'post',
                            data: form,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                log(name + ' begin sending!');
                                options.beforeSend();
                            },
                            success: function (response) {
                                if (JSON.parse(response)['OK']) {
                                    log('Upload End: ' + new Date(Date.now()).toLocaleTimeString());
                                    log('Takes: ' + (Date.now() - begin) + 'ms');
                                    options.afterSuccess(1);
                                }
                                else {
                                    log('Upload Error: ' + response);
                                }
                            },
                            error: function () {
                                log('ERROR: cannot connect to server');
                            }
                        });
                        return;
                    }

                    //chunk upload
                    var sending = 0;
                    for (; sending < Math.min(4, chunkNum); sending++) {
                        uploadChunk(sending);
                    }
                    function uploadChunk(i) {
                        var reader = new FileReader(),
                            start = i * chunkSize,
                            end = Math.min(size, start + chunkSize);

                        reader.onloadend = function () {
                            $.ajax({
                                xhr: function() {
                                    var xhr = new window.XMLHttpRequest();
                                    //Upload progress
                                    xhr.upload.addEventListener("progress", function(event){
                                        if (event.lengthComputable) {
                                            options.progress(i, event.loaded / event.total * 100);
                                        }
                                    }, false);
                                    return xhr;
                                },
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
                                    options.beforeSend();
                                },
                                success: function (response) {
                                    if (JSON.parse(response)['OK']) {
                                        options.chunkSuccess(i, ++succeed, chunkNum);
                                        if (succeed === chunkNum) {
                                            log('Upload End: ' + new Date(Date.now()).toLocaleTimeString());
                                            log('Takes: ' + (Date.now() - begin) + 'ms');
                                            options.afterSuccess(chunkNum);
                                        }
                                        if (sending < chunkNum) {
                                            uploadChunk(sending++);
                                        }
                                    }
                                    else {
                                        log('Upload Error: ' + response);
                                    }
                                },
                                error: function () {
                                    log('ERROR: cannot connect to server');
                                }
                            });
                        };
                        reader.readAsDataURL(file.slice(start, end));
                    }
                },
                error: function () {
                    log('ERROR: cannot connect to server');
                }
            });
        });
    };
}(jQuery));