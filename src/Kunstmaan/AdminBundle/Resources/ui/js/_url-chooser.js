var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.urlChooser = (function(window, undefined) {

    var init, urlChooser,
        saveUrlChooserModal, saveMediaChooserModal, getUrlParam;

    var itemUrl, itemId, itemTitle, itemThumbPath,
        $body = $('body');


    init = function() {
        urlChooser();
    };


    // URL-Chooser
    urlChooser = function() {

        // Link Chooser select
        $body.on('click', '.js-url-chooser-link-select', function(e) {
            e.preventDefault();

            var $this = $(this),
                slug = $this.data('slug'),
                id = $this.data('id');

            // Update preview
            $('#url-chooser__selection-preview').text('Selection: ' + slug);

            // Store values
            itemUrl = slug;
            itemId = id;
        });

        // Media Chooser select
        $body.on('click', '.js-url-chooser-media-select', function(e) {
            e.preventDefault();

            var $this = $(this),
                path = $this.data('path'),
                thumbPath = $this.data('thumb-path'),
                id = $this.data('id'),
                title = $this.data('title'),
                cke = $this.data('cke');

            // Store values
            itemUrl = path;
            itemId = id,
            itemTitle = title;
            itemThumbPath = thumbPath;

            // Save
            if(!cke) {
                var isMediaChooser = $(window.frameElement).closest('.js-ajax-modal').data('media-chooser');

                if(isMediaChooser) {
                    saveMediaChooserModal(false);
                } else {
                    saveUrlChooserModal(false);
                }

            } else {
                saveMediaChooserModal(true);
            }
        });


        // Cancel
        $('#cancel-url-chooser-modal').on('click', function() {
            var cke = $(this).data('cke');

            if(!cke) {
                var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                    parentModalId = $parentModal.attr('id');

                parent.$('#' + parentModalId).modal('hide');

            } else {
                window.close();
            }
        });


        // OK
        $('#save-url-chooser-modal').on('click', function() {
            var cke = $(this).data('cke');

            saveUrlChooserModal(cke);
        });
    };


    // Save for URL-chooser
    saveUrlChooserModal = function(cke) {
        if(!cke) {
            var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                linkedInputId = $parentModal.data('linked-input-id'),
                parentModalId = $parentModal.attr('id');

            // Set val
            parent.$('#' + linkedInputId).val(itemUrl);

            // Close modal
            parent.$('#' + parentModalId).modal('hide');

        } else {
            var funcNum = getUrlParam('CKEditorFuncNum');

            // Set val
            window.opener.CKEDITOR.tools.callFunction(funcNum, itemUrl);

            // Close window
            window.close();
        }
    };


    // Save for Media-chooser
    saveMediaChooserModal = function(cke) {
        if(!cke) {
            var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                linkedInputId = $parentModal.data('linked-input-id'),
                parentModalId = $parentModal.attr('id');

                // Set val
                parent.$('#' + linkedInputId).val(itemId);

                // Update preview
                var $mediaChooser = parent.$('#' + linkedInputId + '-widget'),
                    $previewImg = parent.$('#' + linkedInputId + '__preview__img'),
                    $previewTitle = parent.$('#' + linkedInputId + '__preview__title');

                $mediaChooser.addClass('media-chooser--choosen');
                $previewImg.attr('src', itemThumbPath);
                $previewTitle.html(itemTitle);

            // Close modal
            parent.$('#' + parentModalId).modal('hide');

        } else {
            var funcNum = getUrlParam('CKEditorFuncNum');

            // Set val
            window.opener.CKEDITOR.tools.callFunction(funcNum, itemUrl);

            // Close window
            window.close();
        }
    };


    // Get Url Parameters
    getUrlParam = function(paramName) {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i'),
            match = window.location.search.match(reParam);

        return (match && match.length > 1) ? match[1] : '';
    };


    return {
        init: init
    };

}(window));
