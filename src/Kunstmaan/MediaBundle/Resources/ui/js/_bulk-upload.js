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
