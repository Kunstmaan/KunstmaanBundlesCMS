var kunstmaanTranslatorBundle = kunstmaanTranslatorBundle || {};

kunstmaanTranslatorBundle.inlineEdit = (function(window, undefined) {

    var init;

    init = function() {
        $.fn.editable.defaults.mode = 'inline';

        $('.js-editable').each(function() {
            var $field = $(this);
            var width = $field.width();

            $field.on('shown', function(e, editable) {
                editable.input.$input.css('width', width + 50);
            });

            $field.editable({
                showbuttons: 'bottom',
                emptytext: $field.data('empty-text'),
                pk: function() {
                    return $field.data('uid');
                },
                params: function(params) {
                    params.locale = $field.data('locale');
                    params.domain = $field.data('domain');
                    params.keyword = $field.data('keyword');
                    params.translationId = $field.data('tid');
                    return params;
                },
                success: function(response, newValue) {
                    if (response.success) {
                        $field.data('uid', response.uid);
                    }
                }
            });
        });
    };


    return {
        init: init
    };

}(window));
