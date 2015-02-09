var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pageTemplateEditor = (function(window, undefined) {

    var init,
        changeTemplate;

    init = function() {
        $('.js-change-page-template').on('click', function() {
            changeTemplate($(this));
        });

    };

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

    return {
        init: init
    };

}(window));
