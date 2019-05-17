var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.datepicker = (function($, window, undefined) {

    var init, reInit, _setDefaultDate, _initDatepicker;

    var _today = window.moment(),
        _tomorrow = window.moment(_today).add(1, 'days');

    var defaultFormat = 'DD-MM-YYYY',
        defaultCollapse = true,
        defaultKeepOpen = false,
        defaultMinDate = false,
        defaultShowDefaultDate = false,
        defaultStepping = 1;


    init = function() {
        $('.js-datepicker').each(function() {
            _initDatepicker($(this));
        });
    };

    reInit = function(el) {
        if (el) {
            _initDatepicker($(el));
        } else {
            $('.js-datepicker').each(function() {
                if (!$(this).hasClass('datepicker--enabled')) {
                    _initDatepicker($(this));
                }
            });
        }
    };

    _setDefaultDate = function(elMinDate) {
        if(elMinDate === 'tomorrow') {
            return _tomorrow;
        } else {
            return _today;
        }
    };


    _initDatepicker = function($el) {
        // Get Settings
        var elFormat = $el.data('format'),
            elCollapse = $el.data('collapse'),
            elKeepOpen = $el.data('keep-open'),
            elMinDate = $el.data('min-date'),
            elShowDefaultDate = $el.data('default-date'),
            elStepping = $el.data('stepping');


        // Set Settings
        var format = (elFormat !== undefined) ? elFormat : defaultFormat,
            collapse = (elCollapse !== undefined) ? elCollapse : defaultCollapse,
            keepOpen = (elKeepOpen !== undefined) ? elKeepOpen : defaultKeepOpen,
            minDate = (elMinDate === 'tomorrow') ? _tomorrow : (elMinDate === 'today') ? _today : defaultMinDate,
            defaultDate = (elShowDefaultDate) ? _setDefaultDate(elMinDate) : defaultShowDefaultDate,
            stepping = (elStepping !== undefined) ? elStepping : defaultStepping;

        // Setup
        var $input = $el.find('input'),
            $addon = $el.find('.input-group-addon'),
            linkedDatepickerID = $el.data('linked-datepicker') || false;

        if (format.indexOf('HH:mm') === -1) {
            // Drop time if not necessary
            if (minDate) {
                minDate = minDate.clone().startOf('day'); // clone() because otherwise .startOf() mutates the original moment object
            }

            if (defaultDate) {
                defaultDate = defaultDate.clone().startOf('day');
            }
        }

        $input.datetimepicker({
            format: format,
            collapse: collapse,
            keepOpen: keepOpen,
            minDate: minDate,
            defaultDate: defaultDate,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'auto'
            },
            widgetParent: $el,
            icons: {
                time: 'fa fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-arrow-left',
                next: 'fa fa-arrow-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash'
            },
            stepping: stepping
        });

        $el.addClass('datepicker--enabled');

        $addon.on('click', function() {
            $input.focus();
        });

        // Linked datepickers - allow future datetime only - (un)publish modal
        if (linkedDatepickerID) {
            // set min time only if selected date = today
            $(document).on('dp.change', linkedDatepickerID, function(e) {
                if (e.target.value === _today.format('DD-MM-YYYY')) {
                    var selectedTime = window.moment($input.val(), 'HH:mm');

                    // Force user to select new time, if current time isn't valid anymore
                    selectedTime.isBefore(_today) && $input.data('DateTimePicker').show();

                    $input.data('DateTimePicker').minDate(_today);
                } else {
                    $input.data('DateTimePicker').minDate(false);
                }
            });
        }
    };

    return {
        init: init,
        reInit: reInit
    };

})(jQuery, window);
