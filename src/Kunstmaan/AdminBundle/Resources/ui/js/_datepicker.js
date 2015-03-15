var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.datepicker = (function($, window, undefined) {

    var init;

    var _today = moment(),
        _tomorrow = moment(_today).add(1, 'days');

    var defaultFormat = 'DD-MM-YYYY',
        defaultCollapse = true,
        defaultKeepOpen = false,
        defaultMinDate = false,
        defaultShowDefaultDate = false;


    init = function() {
        $('.js-datepicker').each(function() {
            initDatepicker($(this));
        });
    };


    _setDefaultDate = function(elMinDate) {
        if(elMinDate === 'tomorrow') {
            return _tomorrow;
        } else {
            return _today;
        }
    };


    initDatepicker = function($el) {
        // Get Settings
        var elFormat = $el.data('format'),
            elCollapse = $el.data('collapse'),
            elKeepOpen = $el.data('keep-open'),
            elMinDate = $el.data('min-date'),
            elShowDefaultDate = $el.data('default-date');


        // Set Settings
        var format = (elFormat !== undefined) ? elFormat : defaultFormat,
            collapse = (elCollapse !== undefined) ? elCollapse : defaultCollapse,
            keepOpen = (elKeepOpen !== undefined) ? elKeepOpen : defaultKeepOpen,
            minDate = (elMinDate === 'tomorrow') ? _tomorrow : (elMinDate === 'today') ? _today : defaultMinDate,
            defaultDate = (elShowDefaultDate) ? _setDefaultDate(elMinDate) : defaultShowDefaultDate;


        // Setup
        var $container = $el.find('.input-group'),
            $input = $el.find('input'),
            $addon = $el.find('.input-group-addon');

        $input.datetimepicker({
            format: format,
            collapse: collapse,
            keepOpen: keepOpen,
            minDate: minDate,
            defaultDate: defaultDate,
            widgetParent: $el,
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-arrow-left',
                next: 'fa fa-arrow-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash-o'
            }
        });

        $addon.on('click', function() {
            $input.focus();
        });
    };


    return {
        init: init
    };

}(jQuery, window));
