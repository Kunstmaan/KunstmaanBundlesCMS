var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.richEditor = (function(window, undefined) {

    var _ckEditorConfigs = {
        'kumaDefault': [
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
    };

    var init,
        enableRichEditor, destroyAllRichEditors, destroySpecificRichEditor,
        _collectEditorConfigs;

    // First Init
    init = function() {
        // This object is declared global in _ckeditor_configs.html.twig
        _collectEditorConfigs(window.ckEditorConfigs);

        $('.js-rich-editor').each(function() {
            if (!$(this).hasClass('js-rich-editor--enabled')) {
                enableRichEditor($(this));
            }
        });
    };

    // PRIVATE
    _collectEditorConfigs = function(customConfigs) {
        for (var key in customConfigs) {
            // Do not allow overriding of the fallback config.
            if (key === 'kumaDefault') {
                throw new Error('kumaDefault is a reserved name for the default Kunstmaan ckeditor configuration. Please choose another name.');
            } else {
                _ckEditorConfigs[key] = customConfigs[key];
            }
        }
    };

    // PUBLIC
    enableRichEditor = function($el) {
        var $body = $('body'),
            fileBrowseUrl = $body.data('file-browse-url'),
            imageBrowseUrl = $body.data('image-browse-url'),
            elId = $el.attr('id'),
            elHeight, elEnterMode, elShiftEnterMode, elToolbar;


        // Set Height
        if ($el.attr('height')) {
            elHeight = $el.attr('height');
        } else {
            elHeight = 300;
        }


        // Paragraphs allowed?
        if ($el.attr('noparagraphs')) {
            elEnterMode = CKEDITOR.ENTER_BR;
            elShiftEnterMode = CKEDITOR.ENTER_P;
        } else {
            elEnterMode = CKEDITOR.ENTER_P;
            elShiftEnterMode = CKEDITOR.ENTER_BR;
        }

        elToolbar = (_ckEditorConfigs.hasOwnProperty($el.data('editor-mode'))) ? _ckEditorConfigs[$el.data('editor-mode')] : _ckEditorConfigs['kumaDefault'];
        customConfigFile = customConfigFile || '';

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

            toolbar: elToolbar,
            customConfig: customConfigFile
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
