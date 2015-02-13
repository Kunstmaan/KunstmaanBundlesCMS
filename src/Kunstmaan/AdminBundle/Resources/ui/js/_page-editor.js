var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pageEditor = (function(window, undefined) {

    var init,
        changeTemplate, publishLater, unpublishLater,
        urlChooser, slugChooser,
        initSortable;


    init = function() {
        $('.js-change-page-template').on('click', function() {
            changeTemplate($(this));
        });

        if($('#publish-later__check').length) {
            publishLater();
        }

        if($('#unpublish-later__check').length) {
            unpublishLater();
        }

        if($('.js-sortable-container').lenght) {
            initSortable();
        };

        if($('#slug-chooser').length) {
            slugChooser();
        }

        urlChooser();
    };


    // Change Page Template
    changeTemplate = function($btn) {
        var $holder = $('#pagetemplate_template_holder'),
            $checkedTemplateCheckbox = $('input[name=pagetemplate_template_choice]:checked'),
            newValue = $checkedTemplateCheckbox.val(),
            modal = $btn.data(modal);

        // Hide modal
        $(modal).modal('hide');

        // Update hidden field with new value
        $holder.val(newValue);

        // Submit closest form
        $checkedTemplateCheckbox.closest('form').submit();
    };


    // URL-Chooser
    urlChooser = function() {
        var $body = $('body'),
            itemUrl, itemId;

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


        // Cancel
        $('#cancel-url-chooser-modal').on('click', function() {
            parent.$('#urlChooserModal').modal('hide');
        });


        // OK
        $('#save-url-chooser-modal').on('click', function() {
            var result = {
                path: itemUrl,
                id: itemId
            };

            // Set val
            var linkedInputId = parent.$('#urlChooserModal').data('linked-input-id');
            parent.$('#' + linkedInputId).val(itemUrl);

            // Close modal
            parent.$('#urlChooserModal').modal('hide');


            // OLD
            // function handleOK(result) {
            //     if (window.opener) {
            //         {% if cke %}
            //             var funcNum = getUrlParam('CKEditorFuncNum');
            //             window.opener.CKEDITOR.tools.callFunction(funcNum, result['path']);
            //         {% else %}
            //             window.opener.dialogWin.returnedValue = result;
            //             window.opener.dialogWin.returnFunc()
            //         {% endif %}
            //     } else {
            //         //alert("You have closed the main window.\n\nNo action will be taken on the choices in this dialog box.")
            //     }

            //     window.close();
            //     return false
            // }

            // function getUrlParam(paramName) {
            //     var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
            //     var match = window.location.search.match(reParam) ;
            //     return (match && match.length > 1) ? match[1] : '' ;
            // }
        });


        // OLD
        // $(document).ready(function() {
        //     $('.choosebutton{{ id }}').on('click', function(ev) {
        //         ev.preventDefault();
        //         openDGDialog('{{ path('KunstmaanNodeBundle_selecturl') }}', 580, 500, function(param){
        //             var widget = jQuery('#{{ id }}_widget');
        //             widget.find('input').val(dialogWin.returnedValue.path);
        //         });
        //     });
        // });
    };


    // Slug-Chooser
    slugChooser = function() {
        var _updateSlugPreview, _resetSlug;

        var $widget = $('#slug-chooser'),
            $input = $('#slug-chooser__input'),
            $preview = $('#slug-chooser__preview'),
            $resetBtn = $('#slug-chooser__resetbtn'),
            resetValue = $widget.data('reset')
            urlprefix = $widget.data('url-prefix');

        // Setup url prefix
        if(urlprefix.length == 0 || urlprefix.indexOf('/', urlprefix.length - 1) == -1) { //endwidth
            urlprefix += '/';
        }

        // Update function
        _updateSlugPreview = function() {
            var inputValue = $input.val();

            $preview.html('url: ' + urlprefix + inputValue);
        };
        $input.on('change', _updateSlugPreview);
        $input.on('keyup', _updateSlugPreview);

        // Reset
        _resetSlug = function() {
            $input.val(resetValue);
            _updateSlugPreview();
        };
        $resetBtn.on('click', _resetSlug);

        // Set initial value
        _updateSlugPreview();

        // OLD
        // var updateSlugPreview = function(){
        //     var urlprefix = '{{ path('_slug', {'url': prefix|default('')})}}';
        //     if(urlprefix.length == 0 || urlprefix.indexOf('/', urlprefix.length - 1) == -1) { //endwidth
        //         urlprefix += '/';
        //     }
        //     jQuery('#{{ id }}_preview').html('{{ 'url' | trans }}: '+urlprefix+jQuery('#{{ id }}').val());
        // };
        // var resetSlug = function(e) {
        //     jQuery('#{{ id }}').val(jQuery('#{{ id }}').data('reset'));
        //     jQuery('#{{ id }}').change();
        //     e.preventDefault();
        //     return false;
        // }
        // jQuery('#{{ id }}').change(updateSlugPreview);
        // jQuery('#{{ id }}').keyup(updateSlugPreview);
        // jQuery('#{{ id }}_resetbtn').click(resetSlug);
        // updateSlugPreview();
    };


    // Publish
    publishLater = function() {
        var _toggle;

        _toggle = function(check) {
            if(check.checked) {
                $('#publish-later').show();
                $('#publish-later-action').show();
                $('#publish-action').hide();
            } else {
                $('#publish-later').hide();
                $('#publish-later-action').hide();
                $('#publish-action').show();
            }
        };

        if($('#publish-later__check')) {
            var check = document.getElementById('publish-later__check');

            _toggle(check);

            $(check).on('change', function() {
                _toggle(this);
            });
        }
    };


    // Unpublish
    unpublishLater = function() {
        var _toggle = function(check) {
            if(check.checked) {
                $('#unpublish-later').show();
                $('#unpublish-later-action').show();
                $('#unpublish-action').hide();
            } else {
                $('#unpublish-later').hide();
                $('#unpublish-later-action').hide();
                $('#unpublish-action').show();
            }
        };

        if($('#unpublish-later__check')) {
            var check = document.getElementById('unpublish-later__check');

            _toggle(check);

            $(check).on('change', function() {
                _toggle(this);
            });
        }
    };


    // Sortable
    // TODO: allow groups
    initSortable = function() {
        $('.js-sortable-container').each(function() {
            var id = $(this).attr('id'),
                el = document.getElementById(id);

            Sortable.create(el, {
                draggable: '.js-sortable-item',
                handle: '.js-sortable-item__handle',
                ghostClass: 'sortable-item--ghost',

                scroll: true,
                scrollSensitivity: 30,
                scrollSpeed: 10,

                onStart: function(evt) {
                    $('body').addClass('sortable-active');
                },

                onEnd: function(evt) {
                    $('body').removeClass('sortable-active');
                }
            });
        });

        $('.js-sortable-item__handle').on('mousedown', function() {
            $('body').addClass('sortable-active');
        });

        $('.js-sortable-item__handle').on('mouseup', function() {
            $('body').removeClass('sortable-active');
        });



        // OLD

        // var scope = $(this).closest('section').data('scope');
        // $('.pageparts_sortable[data-scope~=' + scope + ']')
        //         .addClass('connectedSortable')
        //         .sortable('option', 'connectWith', '.connectedSortable');
        // $('.pageparts_sortable:not([data-scope~=' + scope + '])')
        //         .sortable('disable')
        //         .sortable('option', 'connectWith', false)
        //         .parent().addClass('region-disabled');
        // $('.template-block-content').not('.sortable')
        //         .parent().addClass('region-disabled');


        // $(document).ready(function () {
            // var heightenEmptyDropZones = function (elementHeight) {
            //     var $empty = jQuery('.connectedSortable:not(:has(>section))');
            //     $empty.css({'height': elementHeight});
            // }

            // var autoHeightDropZones = function () {
            //     jQuery('.connectedSortable').css({'height': ''});
            // }

            // $('.prop_bar').mousedown(PagePartEditor.propBarMouseDownHandler);
            // $('body').mouseup(
            //         function () {
            //             if (PagePartEditor.sortableClicked) {
            //                 // Enable all sortable regions again
            //                 $('.pageparts_sortable')
            //                         .sortable('enable')
            //                         .sortable('option', 'connectWith', false)
            //                         .parent().removeClass('region-disabled');
            //                 $('.template-block-content').not('.sortable')
            //                         .parent().removeClass('region-disabled');
            //                 PagePartEditor.sortableClicked = false;
            //             }
            //         }
            // );
            // $('#parts_{{pagepartadmin.context}}').sortable({
            //     iframeFix: true,
            //     connectWith: ".connectedSortable",
            //     handle: '.prop_bar',
            //     cursor: 'move',
            //     placeholder: "placeholder",
            //     forcePlaceholderSize: true,
            //     tolerance: "pointer",
            //     revert: 100,
            //     opacity: 1,
            //     start: function (e, ui) {
            //         $(ui.item).find('.new_pagepart').html('');
            //         disableCKEditors();
            //         $('.draggable').css('opacity', ".4");
            //         $('.ui-sortable-helper .new_pagepart').slideUp("fast");

            //         // Temporarily change the height of empty pagepart containers.
            //         heightenEmptyDropZones(ui.item.outerHeight(true));
            //     },
            //     stop: function (e, ui) {
            //         $(ui.item).find('.new_pagepart').html($(ui.item).parents('.pagepartscontainer').find('.new_pagepart.first').html());
            //         //update context names
            //         var context = $(ui.item).parents('.pagepartscontainer').data('context');
            //         $(ui.item).find('.pagepartadmin_field_updatecontextname').each(function () {
            //             $(this).attr('name', context + $(this).data('suffix'));
            //         });
            //         enableCKEditors();

            //         // Revert the height for the dropzones.
            //         autoHeightDropZones();

            //         // Remove connectedSortable when stopped dropping.
            //         $('.connectedSortable').removeClass('connectedSortable');

            //         $('.draggable').css('opacity', "1");
            //     }
            // });
        // });
    };


    return {
        init: init
    };

}(window));
