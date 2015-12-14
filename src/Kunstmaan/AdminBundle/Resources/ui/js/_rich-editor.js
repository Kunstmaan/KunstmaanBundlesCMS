var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.richEditor = (function(window, undefined) {

    var editor; // Holds the editor var when overriding the dialog's onOk method.

    var _ckEditorConfigs = {
        'kumaDefault': {
            skin: 'bootstrapck',
            startupFocus: false,
            bodyClass: 'CKEditor',
            filebrowserWindowWidth: 970,
            filebrowserImageWindowWidth: 970,
            filebrowserImageUploadUrl: '',
            extraAllowedContent: 'div(*)',
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

    var init, reInit,
        enableRichEditor, destroyAllRichEditors, destroySpecificRichEditor,
        _collectEditorConfigs, _collectExternalPlugins, _customOkFunctionForTables;

    // First Init
    init = reInit = function() {
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

    // This is a slightly dirty hack to enable us to make nicer responsive tables.
    // Basically we override the onOk method from the tables plugin from ckSource, and add a wrapper div.
    // By adding a wrapper div we can overflow:scroll; on smaller screens which is a lot nicer.
    _customOkFunctionForTables = function() {

        var makeElement = function( name ) {
            return new CKEDITOR.dom.element( name, editor.document );
        };

        var selection = editor.getSelection(),
            bms = this._.selectedElement && selection.createBookmarks();

        var data = {},
            table = this._.selectedElement || makeElement('table'),
            wrapper = makeElement('div');

        wrapper.setAttribute('class', 'table-wrapper');
        editor.insertElement(wrapper);

        this.commitContent( data, table );

        if ( data.info ) {
            var info = data.info;

            // Generate the rows and cols.
            if ( !this._.selectedElement ) {
                var tbody = table.append( makeElement( 'tbody' ) ),
                    rows = parseInt( info.txtRows, 10 ) || 0,
                    cols = parseInt( info.txtCols, 10 ) || 0;

                for ( var i = 0; i < rows; i++ ) {
                    var row = tbody.append( makeElement( 'tr' ) );
                    for ( var j = 0; j < cols; j++ ) {
                        var cell = row.append( makeElement( 'td' ) );
                        cell.appendBogus();
                    }
                }
            }

            // Modify the table headers. Depends on having rows and cols generated
            // correctly so it can't be done in commit functions.

            // Should we make a <thead>?
            var headers = info.selHeaders;
            if ( !table.$.tHead && ( headers == 'row' || headers == 'both' ) ) {
                var thead = new CKEDITOR.dom.element( table.$.createTHead() );
                tbody = table.getElementsByTag( 'tbody' ).getItem( 0 );
                var theRow = tbody.getElementsByTag( 'tr' ).getItem( 0 );

                // Change TD to TH:
                for ( i = 0; i < theRow.getChildCount(); i++ ) {
                    var th = theRow.getChild( i );
                    // Skip bookmark nodes. (#6155)
                    if ( th.type == CKEDITOR.NODE_ELEMENT && !th.data( 'cke-bookmark' ) ) {
                        th.renameNode( 'th' );
                        th.setAttribute( 'scope', 'col' );
                    }
                }
                thead.append( theRow.remove() );
            }

            if ( table.$.tHead !== null && !( headers == 'row' || headers == 'both' ) ) {
                // Move the row out of the THead and put it in the TBody:
                thead = new CKEDITOR.dom.element( table.$.tHead );
                tbody = table.getElementsByTag( 'tbody' ).getItem( 0 );

                var previousFirstRow = tbody.getFirst();
                while ( thead.getChildCount() > 0 ) {
                    theRow = thead.getFirst();
                    for ( i = 0; i < theRow.getChildCount(); i++ ) {
                        var newCell = theRow.getChild( i );
                        if ( newCell.type == CKEDITOR.NODE_ELEMENT ) {
                            newCell.renameNode( 'td' );
                            newCell.removeAttribute( 'scope' );
                        }
                    }
                    theRow.insertBefore( previousFirstRow );
                }
                thead.remove();
            }

            // Should we make all first cells in a row TH?
            if ( !this.hasColumnHeaders && ( headers == 'col' || headers == 'both' ) ) {
                for ( row = 0; row < table.$.rows.length; row++ ) {
                    newCell = new CKEDITOR.dom.element( table.$.rows[ row ].cells[ 0 ] );
                    newCell.renameNode( 'th' );
                    newCell.setAttribute( 'scope', 'row' );
                }
            }

            // Should we make all first TH-cells in a row make TD? If 'yes' we do it the other way round :-)
            if ( ( this.hasColumnHeaders ) && !( headers == 'col' || headers == 'both' ) ) {
                for ( i = 0; i < table.$.rows.length; i++ ) {
                    row = new CKEDITOR.dom.element( table.$.rows[ i ] );
                    if ( row.getParent().getName() == 'tbody' ) {
                        newCell = new CKEDITOR.dom.element( row.$.cells[ 0 ] );
                        newCell.renameNode( 'td' );
                        newCell.removeAttribute( 'scope' );
                    }
                }
            }

            // Set the width and height.
            info.txtHeight ? table.setStyle( 'height', info.txtHeight ) : table.removeStyle( 'height' );
            info.txtWidth ? table.setStyle( 'width', info.txtWidth ) : table.removeStyle( 'width' );

            if ( !table.getAttribute( 'style' ) )
                table.removeAttribute( 'style' );
        }

        // Insert the table element if we're creating one.
        if ( !this._.selectedElement ) {
            wrapper.append(table);
            // Override the default cursor position after insertElement to place
            // cursor inside the first cell (#7959), IE needs a while.
            setTimeout( function() {
                var firstCell = new CKEDITOR.dom.element( table.$.rows[ 0 ].cells[ 0 ] );
                var range = editor.createRange();
                range.moveToPosition( firstCell, CKEDITOR.POSITION_AFTER_START );
                range.select();
            }, 0 );
        }
        // Properly restore the selection, (#4822) but don't break
        // because of this, e.g. updated table caption.
        else {
            try {
                selection.selectBookmarks( bms );
            } catch ( er ) {
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
        CKEDITOR.on('dialogDefinition', function(e) {
            if (e.data.name === 'table') {
                editor = e.editor;
                e.data.definition.onOk = _customOkFunctionForTables;
            }
        });

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
        reInit: reInit,
        enableRichEditor: enableRichEditor,
        destroyAllRichEditors: destroyAllRichEditors,
        destroySpecificRichEditor: destroySpecificRichEditor
    };

}(window));
