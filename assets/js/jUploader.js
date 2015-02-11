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
            fileSelector: null,
            singleSize: 4 * 1024 * 1024,    //default: 4MB
            chunkSize: 4 * 1024 * 1024,     //default: 4MB
            afterSuccess: function(total) {},
            chunkSuccess: function(index, completeNum, total) {}
        }, options);

        $(options.buttonSelector).click(function() {
            var file = $(options.fileSelector)[0].files[0],
                name = file.name,
                size = file.size,
                succeed = 0,
                chunkSize = options.chunkSize,
                chunkNum = Math.ceil(size / chunkSize);

            // if size of the file is no more than critical size
            if (size <= options.singleSize) {
                form = new FormData();
                form.append("fileData", file);
                form.append("name", name);
                form.append("total", 1);
                form.append("index", 0);

                $.ajax({
                    url: options.url,
                    type: 'post',
                    data: form,
                    async: true,
                    processData: false,
                    contentType: false,
                    success: function () {
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
                        var form = new FormData();
                        form.append("fileData", reader.result);
                        form.append("name", name);
                        form.append("total", chunkNum);
                        form.append("index", i);

                        $.ajax({
                            url: options.url,
                            type: 'post',
                            data: form,
                            async: true,
                            processData: false,
                            contentType: false,
                            success: function () {
                                options.chunkSuccess(i, ++succeed, chunkNum);
                                if (succeed === chunkNum) {
                                    options.afterSuccess(chunkNum);
                                }
                            }
                        });
                    };
                    reader.readAsBinaryString(file.slice(start, end));
                }(i));

            }
        });
    };
}(jQuery));