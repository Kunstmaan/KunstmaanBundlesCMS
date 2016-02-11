var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.iconChooser = (function(window, undefined) {

    var CSS_ID = 'iconfont';

    var init, chooseIcon, _configureWidget, _removeIcon;

    var $widgets = $('.js-icon-chooser');

    _configureWidget = function(widget) {
        var $widget = $(widget),
            $closeButton = $widget.find('.js-icon-chooser__close');

        if ($('#' + CSS_ID).length <= 0) {
            $('head').append('<link id="' + CSS_ID + '" rel="stylesheet" type="text/css" href="' + $widget.data('css-link') + '" media="all">');
        }

        if ($widget.find('.js-icon-chooser__input').val() !== '') {
            $widget.addClass('media-chooser--choosen');
        }

        $closeButton.on('click', function() {
            _removeIcon(this);
        });
    };

    _removeIcon = function(button) {
        var $button = $(button),
            $widget = $button.closest('.icon-chooser'),
            $input = $widget.find('.js-icon-chooser__input'),
            $preview = $widget.find('.js-icon-chooser__preview'),
            _class = $widget.find('.js-icon-chooser__input').val();

        $widget.removeClass('media-chooser--choosen');
        $preview.removeClass(_class);

        $input.val('');
    };

    chooseIcon = function(e, icon, id) {
        e.preventDefault();

        var $widget = $('#' + id + '_widget'),
            $modal = $('#' + id + '_iconChooserModal');

        $widget.find('.js-icon-chooser__input').val(icon);
        $widget.find('.js-icon-chooser__preview').addClass(icon);

        $widget.addClass('media-chooser--choosen');

        $modal.modal('hide');
    };

    init = function() {
        $widgets.each(function() {
            _configureWidget(this);
        });

        $('.js-icon-chooser__icon').on('click', function(e) {
            window.parent.kunstmaanbundles.iconChooser.chooseIcon(e, $(this).data('icon-class'), $(window.frameElement).closest('.js-ajax-modal').data('widget-id'));
        });
    };

    return {
        init: init,
        chooseIcon: chooseIcon
    };

})(window);
