var kunstmaanMediaBundle = kunstmaanMediaBundle || {};

kunstmaanMediaBundle.dndUpload = (function(window, undefined) {

    var init, initUpload;


    // Init
    init = function() {
        var $area = $('#dnd-area'),
            $container = $('#dnd-container'),
            $status = $('#dnd-area__upload-status'),
            dropUrl = $area.data('drop-url'),
            currentUrl = $area.data('current-url');

        initUpload($area, $container, $status, dropUrl, currentUrl);
    };


    // Upload
    initUpload = function($area, $container, $status, dropUrl, currentUrl) {
        var dndUploader = new plupload.Uploader({
                runtimes : 'html5',
                dragdrop: true,
                drop_element: 'dnd-area',
                browse_button : 'dnd-area-link',
                url: dropUrl,
                processing_id: null,

                filters : {
                    max_file_size : '100mb'
                },

                init: {
                    PostInit: function() {
                        $(window).on('dragenter', function() {
                            $area.addClass('dnd-area--dragover');
                        });

                        $area.on('dragleave drop dragend', function() {
                            $area.removeClass('dnd-area--dragover');
                        });
                    },

                    FilesAdded: function(up, files) {
                        plupload.each(files, function(file) {
                            $status.append('<li class="list-group-item" id="' + file.id + '">' + file.name + ' (<small>' + plupload.formatSize(file.size) + '</small>) <strong class="js-status"></strong></li>')
                        });

                        dndUploader.start();
                    },

                    UploadProgress: function(up, file) {
                        var $fileLine = $('#' + file.id);

                        $fileLine.find('.js-status').html(file.percent + '%');
                    },

                    UploadComplete: function(up, files) {
                        // Set Loading
                        $('body').addClass('app--loading');

                        $area.addClass('dnd-area--upload-done');

                        window.location = currentUrl;
                    }
                }
            });


        // Initialize uploader
        dndUploader.init();
    };


    return {
        init: init
    };

}(window));
