var kunstmaanMediaBundle = kunstmaanMediaBundle || {};

kunstmaanMediaBundle.bulkUpload = (function(window, undefined) {

    var init, initUploader;

    init = function() {
        initUploader();
    };


    initUploader = function() {
        // Get values and elements
        var $uploader = $('#bulk-upload'),
            url = $uploader.data('url');


        var $fileList = $('#bulk-upload__file-list'),
            $uploadWrapper = $('#bulk-button-wrapper--upload'),
            $completedWrapper = $('#bulk-button-wrapper--completed'),
            $pickFilesBtn = $('#bulk-button--pick-files'),
            $uploadFilesBtn = $('#bulk-button--upload-files');


        // Setup
        var bulkUploader = new plupload.Uploader({
                runtimes : 'html5',
                browse_button: 'bulk-button--pick-files',
                container: 'bulk-upload__container',
                url: url,
                processing_id: null,

                filters : {
                    max_file_size : '100mb'
                },

                init: {
                    PostInit: function() {
                        $fileList.html('<p class="list-group-item">No files selected</p>');

                        $uploadFilesBtn.on('click', function() {
                            bulkUploader.start();
                        });
                    },

                    FilesAdded: function(up, files) {
                        $fileList.html('');

                        plupload.each(files, function(file) {
                            $fileList.append('<li class="list-group-item" id="' + file.id + '">' + file.name + ' (<small>' + plupload.formatSize(file.size) + '</small>) <strong class="js-status"></strong></li>')
                        });

                        $uploadFilesBtn.removeClass('disabled');
                        $uploadFilesBtn.prop('disabled', false);
                        $uploadFilesBtn.addClass('btn-primary');
                        $pickFilesBtn.removeClass('btn-primary').addClass('btn-default');
                    },

                    UploadProgress: function(up, file) {
                        var $fileLine = $('#' + file.id);

                        $fileLine.find('.js-status').html(file.percent + '%');
                    },

                    Error: function(up, err) {
                        var $fileLine = $('#' + up.processing_id);

                        $fileLine.find('.js-status').html('ERROR: ' + err.message);
                    },

                    FileUploaded: function(up, file, res) {
                        var $fileLine = $('#' + file.id);

                        $fileLine.addClass('list-group-item-success');

                        var obj = null;
                        obj = JSON.parse(jsonString);

                        if (obj.error) {
                            $fileLine.addClass('list-group-item-danger');
                            $fileLine.find('.js-status').html('ERROR: ' + obj.error.message);
                        } else {
                            $fileLine.addClass('list-group-item-success');
                        }
                    },

                    UploadComplete: function(up, files) {
                        $completedWrapper.removeClass('hidden');
                    },

                    BeforeUpload: function(up, file) {
                        up.processing_id = file.id;
                        $uploadWrapper.addClass('hidden');
                    }
                }
            });

        // Initialize uploader
        bulkUploader.init();
    };


    return {
        init: init
    };

}(window));


/*

var uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight',
            browse_button : 'pick-files', // you can pass in id...
            container: document.getElementById('bulk-upload-container'),
            url : '{{ path('KunstmaanMediaBundle_media_bulk_upload_submit', { 'folderId' : folder.id }) }}',
            flash_swf_url : '{{ asset('bundles/kunstmaanadmin/js/plupload/Moxie.swf') }}',
            silverlight_xap_url : '{{ asset('bundles/kunstmaanadmin/js/plupload/Moxie.xap') }}',

            processing_id: null,

            filters : {
                max_file_size : '100mb'
            },

            init: {
                PostInit: function() {
                    document.getElementById('file-list').innerHTML = '';
                    document.getElementById('upload-files').onclick = function() {
                        uploader.start();
                        return false;
                    };

                },

                FilesAdded: function(up, files) {
                    plupload.each(files, function(file) {
                        document.getElementById('file-list').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                    });

                    $('#upload-files').removeClass('disabled');
                    $('#upload-files').prop('disabled', false);
                    $('#upload-files').addClass('btn-primary');
                    $('#pick-files').removeClass('btn-primary');
                },

                UploadProgress: function(up, file) {
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                },

                Error: function(up, err) {
                    document.getElementById(up.processing_id).getElementsByTagName('b')[0].innerHTML = '<span>ERROR: ' + err.message + "</span>";
                },

                FileUploaded: function(up, file, res) {
                    var obj = null;
                    if ($.browser.msie && $.browser.version <= 8) {
                        obj = eval('(' + jsonString + ')');
                    } else {
                        obj = JSON.parse(jsonString);
                    }
                    if (obj.error) {
                        document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>ERROR: ' + obj.error.message + "</span>";
                    }
                },

                UploadComplete: function(up, files) {
                    $('#completed-button-wrapper').show();
                },

                BeforeUpload: function(up, file) {
                    up.processing_id = file.id;
                    $('#bulk-button-wrapper').hide();
                }
            }
        });

        uploader.init();

*/
