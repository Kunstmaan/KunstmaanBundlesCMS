var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.richEditor = (function(window, undefined) {

    var init,
        enableRichEditors;


    // First Init
    init = function() {

        $('.js-rich-editor').each(function() {
            enableRichEditors($(this));
        });

    };


    // Enable
    enableRichEditors = function($el) {
        var $body = $('body'),
            fileBrowseUrl = $body.data('file-browse-url'),
            imageBrowseUrl = $body.data('image-browse-url'),
            elId = $el.attr('id'),
            elHeight, elEnterMode, elShiftEnterMode;

        // Height
        if($el.attr('height')) {
            elHeight = $el.attr('height');
        } else {
            elHeight = 500;
        }

        // No-paragraphs
        if($el.attr('noparagraphs')) {
            elEnterMode = CKEDITOR.ENTER_BR;
            elShiftEnterMode = CKEDITOR.ENTER_P;
        } else {
            elEnterMode = CKEDITOR.ENTER_P;
            elShiftEnterMode = CKEDITOR.ENTER_BR;
        }

        // Place CK
        CKEDITOR.replace(elId, {
            skin: 'bootstrapck',
            startupFocus: false,
            height: elHeight,
            bodyClass: 'CKEditor',

            filebrowserBrowseUrl: fileBrowseUrl,
            filebrowserWindowWidth: 580,

            filebrowserImageBrowseUrl: imageBrowseUrl,
            filebrowserImageBrowseLinkUrl: imageBrowseUrl,
            filebrowserImageWindowWidth: 970,

            filebrowserImageUploadUrl: '',

            enterMode: elEnterMode,
            shiftEnterMode: elShiftEnterMode,

            toolbar: [
                {
                    name: 'basicstyles',
                    items : ['Bold','Italic','Underline','Strike','Subscript','Superscript', 'RemoveFormat']
                },
                {
                    name: 'lists',
                    items : ['NumberedList','BulletedList']
                },
                {
                    name: 'dents',
                    items : ['Outdent','Indent']
                },
                {
                    name: 'links',
                    items : ['Link','Unlink', 'Anchor']
                },
                {
                    name: 'insert',
                    items : ['Image', 'SpecialChar']
                },
                {
                    name: 'clipboard',
                    items : ['SelectAll', 'Cut','Copy','PasteText','PasteFromWord','-','Undo','Redo']
                },
                {
                    name: 'editing',
                    items : []
                },
                {
                    name: 'document',
                    items : [ 'Source' ]
                }
            ]
        });

        // Behat tests
        // Add id on iframe so that behat tests can interact
        var checkExist = setInterval(function() {

            if($('#cke_' + elId + ' iframe').length === 1) {
                var parts = elId.split("_"),
                    name = parts[parts.length-1];

                $('#cke_' + elId + ' iframe').attr('id', 'cke_iframe_' + name);

                clearInterval(checkExist);
            }
        }, 250);
    };


    // Destroy
    destroyRichEditors = function() {
        for(instance in CKEDITOR.instances) {

            if($('#' + CKEDITOR.instances[instance].name).hasClass('js-rich-editor')) {
                CKEDITOR.instances[instance].destroy();
            };
        }
    };


    return {
        init: init,
        destroyRichEditors: destroyRichEditors
    };

}(window));
