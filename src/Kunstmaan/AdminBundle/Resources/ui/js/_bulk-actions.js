var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.bulkActions = (function($, window, undefined) {

    var init,
        setAllCheckbox, updateBulkCheckboxes, bulkAction;

    var $form = $('#bulk-form'),
        $SelectAllCheckbox = $('#select-all-bulk-checkbox'),
        $bulkCheckboxes = $('.js-bulk-checkbox'),
        $bulkActionButtons = $('.js-bulk-action-button');


    init = function() {

        if($form.length) {
            setAllCheckbox();

            $bulkCheckboxes.on('change', updateBulkCheckboxes);
            $bulkActionButtons.on('click', function() {
                bulkAction($(this));
            });
        }
    };


    // All
    setAllCheckbox = function() {
        $SelectAllCheckbox.on('change', function() {

            if (this.checked === true) {
                $bulkCheckboxes.prop('checked', true);
                $bulkActionButtons.removeClass('disabled').removeAttr('disabled');
            } else {
                $bulkCheckboxes.prop('checked', false);
                $bulkActionButtons.addClass('disabled').attr('disabled', 'true');
            }
        });
    };


    // Update
    updateBulkCheckboxes = function() {
        var allChecked = true,
            oneChecked = false;

        // Check if all is checked
        $bulkCheckboxes.each(function() {
            if(this.checked === true) {
                oneChecked = true;
            } else {
                allChecked = false;
            }
        });
        $SelectAllCheckbox.prop('checked', allChecked);

        // Check if one or more is checked
        if (oneChecked) {
            $bulkActionButtons.removeClass('disabled').removeAttr('disabled');
        } else {
            $bulkActionButtons.addClass('disabled').attr('disabled', 'true');
        }
    };


    // Action buttons
    bulkAction = function($btn) {
        var formAction = $btn.data('action');

        $form.attr('action', formAction);
        $form.submit();
    };


    return {
        init: init
    };

}(jQuery, window));
