var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.datepicker = (function($, window, undefined) {

    var init;

    init = function() {
        $('.js-datepicker').each(function(key, value) {
            initDatepicker(value);
        });
    };

    initDatepicker = function(el) {
        var $input = $(el).find('input');

        $input.datetimepicker({
            format: 'DD/MM/YYYY',
            collapse: true,
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash-o'
            }
        });

        $(el).find('span.input-group-addon').click(function(e) {
            $input.focus();
        });
    }


    return {
        init: init
    };

}(jQuery, window));
