var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.richEditor = (function(window, undefined) {

    var _ckEditorConfigs = {
        'kumaDefault': {
            skin: 'bootstrapck',
            startupFocus: false,
            height: 300,
            bodyClass: 'CKEditor',
            filebrowserWindowWidth: 970,
            filebrowserImageWindowWidth: 970,
            filebrowserImageUploadUrl: '',
            toolbar: [
                { name: 'basicstyles', items : ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat'] },
                { name: 'lists', items : ['NumberedList', 'BulletedList'] },
                { name: 'dents', items : ['Outdent', 'Indent'] },
                { name: 'links', items : ['Link','Unlink', 'Anchor'] },
                { name: 'insert', items : ['Image', 'Table', 'SpecialChar'] },
                { name: 'clipboard', items : ['SelectAll', 'Cut', 'Copy', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items : [] },
                { name: 'document', items : ['Source'] }
            ]
        }
    };

    var init,
        enableRichEditor, destroyAllRichEditors, destroySpecificRichEditor,
        _collectEditorConfigs, _collectExternalPlugins;

    // First Init
    init = function() {
        // These objects are declared global in _ckeditor_configs.html.twig
        _collectExternalPlugins(window.externalPlugins);
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
                // v3.3.0 breaking: Thse whole config is now configurable, instead of just the toolbar.
                // This means we require an object instead of an array.
                if (customConfigs[key].constructor === Array) {
                    throw new Error('Since v3.3.0 the whole rich editor config is editable. This means a custom config should be an object instead of an array.');
                } else {
                    _ckEditorConfigs[key] = customConfigs[key];
                }
            }
        }
    };

    _collectExternalPlugins = function(plugins) {
        if (plugins !== undefined && plugins.length > 0 && CKEDITOR !== undefined && CKEDITOR.plugins !== undefined) {
            var i = 0;
            for(; i < plugins.length; i++) {
                if (plugins[i].constructor === Array) {
                    CKEDITOR.plugins.addExternal.apply(CKEDITOR.plugins, plugins[i]);
                } else {
                    throw new Error('Plugins should be configured as an Array with the following values: [names, path, fileName] (Filename optional.)')
                }
            }
        }
    };

    // PUBLIC
    enableRichEditor = function($el) {
        var $body = $('body'),
            elementId = $el.attr('id'),
            editorConfig;

        var dataAttrConfiguration = {
            'height': $el.attr('height') || 300,
            'filebrowserBrowseUrl': $body.data('file-browse-url'),
            'filebrowserImageBrowseUrl': $body.data('image-browse-url'),
            'filebrowserImageBrowseLinkUrl': $body.data('image-browse-url'),
            'enterMode': $el.attr('noparagraphs') ? CKEDITOR.ENTER_BR : CKEDITOR.ENTER_P,
            'shiftEnterMode': $el.attr('noparagraphs') ? CKEDITOR.ENTER_P : CKEDITOR.ENTER_BR,
        }

        editorConfig = (_ckEditorConfigs.hasOwnProperty($el.data('editor-mode'))) ? _ckEditorConfigs[$el.data('editor-mode')] : _ckEditorConfigs['kumaDefault'];

        // Load the data from data attrs, but don't override the ones in the config if they're set.
        for (key in dataAttrConfiguration) {
            if (editorConfig[key] === undefined) {
                editorConfig[key] = dataAttrConfiguration[key];
            }
        }

        // Place CK
        CKEDITOR.replace(elementId, editorConfig);

        $el.addClass('js-rich-editor--enabled');

        // Behat tests
        // Add id on iframe so that behat tests can interact
        var checkExist = setInterval(function() {

            if($('#cke_' + elementId + ' iframe').length === 1) {
                var parts = elementId.split("_"),
                    name = parts[parts.length-1];

                $('#cke_' + elementId + ' iframe').attr('id', 'cke_iframe_' + name);

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
        var elementId = $el.attr('id'),
            editor = CKEDITOR.instances[elementId];

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
