var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.richEditor = (function(window, undefined) {

    var init,
        enableRichEditor, destroyAllRichEditors, destroySpecificRichEditor;


    // First Init
    init = function() {
        $('.js-rich-editor').each(function() {
            if(!$(this).hasClass('js-rich-editor--enabled')) {
                enableRichEditor($(this));
            }
        });
    };


    // Enable
    enableRichEditor = function($el) {
        var $body = $('body'),
            fileBrowseUrl = $body.data('file-browse-url'),
            imageBrowseUrl = $body.data('image-browse-url'),
            elId = $el.attr('id'),
            elHeight, elEnterMode, elShiftEnterMode, elToolbar;


        // Set Height
        if($el.attr('height')) {
            elHeight = $el.attr('height');
        } else {
            elHeight = 300;
        }


        // Paragraphs allowed?
        if($el.attr('noparagraphs')) {
            elEnterMode = CKEDITOR.ENTER_BR;
            elShiftEnterMode = CKEDITOR.ENTER_P;
        } else {
            elEnterMode = CKEDITOR.ENTER_P;
            elShiftEnterMode = CKEDITOR.ENTER_BR;
        }


        // Toolbar options
        if($el.data('simple') === true) {
            elToolbar = [
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'RemoveFormat']
                }
            ]
        } else if($el.data('full') === true) {
            elToolbar = [
                {
                    name: 'basicstyles',
                    items : ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    groups: [ 'align' ],
                    items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                },
                {
                    name: 'lists',
                    items : ['NumberedList', 'BulletedList']
                },
                {
                    name: 'dents',
                    items : ['Outdent', 'Indent']
                },
                {
                    name: 'links',
                    items : ['Link','Unlink', 'Anchor']
                },
                {
                    name: 'insert',
                    items : ['Image', 'Table', 'SpecialChar']
                },
                {
                    name: 'clipboard',
                    items : ['SelectAll', 'Cut', 'Copy', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                },
                {
                    name: 'editing',
                    items : []
                },
                {
                    name: 'styles',
                    items: [ 'Styles', 'Format', 'Font', 'FontSize' ]

                },
                {
                    name: 'colors',
                    items: [ 'TextColor', 'BGColor' ]
                },
                {
                    name: 'document',
                    items : ['Source']
                }
            ]
        } else {
            elToolbar = [
                {
                    name: 'basicstyles',
                    items : ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat']
                },
                {
                    name: 'lists',
                    items : ['NumberedList', 'BulletedList']
                },
                {
                    name: 'dents',
                    items : ['Outdent', 'Indent']
                },
                {
                    name: 'links',
                    items : ['Link','Unlink', 'Anchor']
                },
                {
                    name: 'insert',
                    items : ['Image', 'Table', 'SpecialChar']
                },
                {
                    name: 'clipboard',
                    items : ['SelectAll', 'Cut', 'Copy', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                },
                {
                    name: 'editing',
                    items : []
                },
                {
                    name: 'document',
                    items : ['Source']
                }
            ]
        }


        // Place CK
        CKEDITOR.replace(elId, {
            skin: 'bootstrapck',
            startupFocus: false,
            height: elHeight,
            bodyClass: 'CKEditor',

            filebrowserBrowseUrl: fileBrowseUrl,
            filebrowserWindowWidth: 970,

            filebrowserImageBrowseUrl: imageBrowseUrl,
            filebrowserImageBrowseLinkUrl: imageBrowseUrl,
            filebrowserImageWindowWidth: 970,

            filebrowserImageUploadUrl: '',

            enterMode: elEnterMode,
            shiftEnterMode: elShiftEnterMode,

            toolbar: elToolbar
        });

        $el.addClass('js-rich-editor--enabled');

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


    // Destroy All
    destroyAllRichEditors = function() {
        for(instance in CKEDITOR.instances) {
            var $el = $('#' + CKEDITOR.instances[instance].name);

            if($el.hasClass('js-rich-editor')) {
                $el.removeClass('js-rich-editor--enabled');

                CKEDITOR.instances[instance].destroy(true);
            };
        }
    };


    // Destroy Specific
    destroySpecificRichEditor = function($el) {
        var elId = $el.attr('id'),
            editor = CKEDITOR.instances[elId];

        if(editor) {
            editor.destroy(true);
        }
    };


    // Returns
    return {
        init: init,
        enableRichEditor: enableRichEditor,
        destroyAllRichEditors: destroyAllRichEditors,
        destroySpecificRichEditor: destroySpecificRichEditor
    };

}(window));
