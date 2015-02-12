var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pageEditor = (function(window, undefined) {

    var init,
        changeTemplate, publish, unpublish,
        initSortable;


    init = function() {
        $('.js-change-page-template').on('click', function() {
            changeTemplate($(this));
        });

        initSortable();
        publish();
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


    // Publish
    publish = function() {
        var _toggle = function(check) {
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
    unpublish = function() {

        // OLD
        // var syncView = function() {
        //  $('#unpub_later').toggle(this.checked);
        //     $('#unpub_publish_action').toggle(!this.checked);
        //     $('#unpub_publishlater_action').toggle(this.checked);
        // };
        // $(syncView);
        // $(function(){
        //  $('#unpub_dtpckr').datepicker().on('changeDate', function(ev){
        //      $('#unpub_date').val(ev.date.getFullYear()+'-'+(ev.date.getMonth()+1)+'-'+ev.date.getDate());
        //     });
        //  $('#unpub_dtpckr').datepicker('setStartDate', new Date());
        //     $('#unpub_tmpckr').timepicker({
        //         minuteStep: 1,
        //         template: 'modal',
        //         showMeridian: false,
        //         showInputs: true
        //     });
        // });
        // $('#unpub_chkbx').change(syncView);
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
