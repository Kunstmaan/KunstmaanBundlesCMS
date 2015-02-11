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

        // OLD
        // var syncView = function() {
        //  $('#pub_later').toggle(this.checked);
        //     $('#pub_publish_action').toggle(!this.checked);
        //     $('#pub_publishlater_action').toggle(this.checked);
        // };
        // $(syncView);
        // $(function(){
        //  $('#pub_dtpckr').datepicker().on('changeDate', function(ev){
        //      $('#pub_date').val(ev.date.getFullYear()+'-'+(ev.date.getMonth()+1)+'-'+ev.date.getDate());
        //     });
        //  $('#pub_dtpckr').datepicker('setStartDate', new Date());
        //     $('#pub_tmpckr').timepicker({
        //         minuteStep: 1,
        //         template: 'modal',
        //         showMeridian: false,
        //         showInputs: true
        //     });
        // });
        // $('#pub_chkbx').change(syncView);
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
    initSortable = function() {
        // $('.js-sortable-container').sortable({
        //     items: '.js-sortable-item',
        //     handle: '.js-sortable-item__handle',
        //     forcePlaceholderSize: true
        // });
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



    };


    return {
        init: init
    };

}(window));
