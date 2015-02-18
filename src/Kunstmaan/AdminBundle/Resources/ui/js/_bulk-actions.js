var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.bulkActions = (function($, window, undefined) {

    var init,
        setAllCheckbox, updateBulkCheckboxes;

    var $SelectAllCheckbox = $('#select-all-bulk-checkbox'),
        $bulkCheckboxes = $('.js-bulk-checkbox'),
        $bulkActionButtons = $('.js-bulk-button');


    init = function() {
        setAllCheckbox();

        $bulkCheckboxes.on('change', updateBulkCheckboxes);
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


    return {
        init: init
    };

}(jQuery, window));
