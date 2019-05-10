var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.urlChooser = (function (window, undefined) {

    var init, urlChooser, saveUrlChooserModal, saveMediaChooserModal, getUrlParam, adaptUrlChooser, endsWith;

    var itemUrl, itemId, itemTitle, itemThumbPath, replacedUrl, $body = $('body');


    init = function () {
        urlChooser();
        adaptUrlChooser();
    };

    // URL-Chooser
    urlChooser = function () {

        // Link Chooser select
        $body.on('click', '.js-url-chooser-link-select a', function (e) {
            e.preventDefault();

            var $this = $(this).parent(),
                slug = $this.data('slug'),
                id = $this.data('id'),
                replaceUrl = $this.closest('nav').data('replace-url');

            // Store values
            itemUrl = (slug ? slug : '');
            itemId = id;

            // Replace URL
            $.ajax({
                url: replaceUrl,
                type: 'GET',
                data: {'text': itemUrl},
                success: function (response) {
                    replacedUrl = response.text;

                    // Update preview
                    $('#url-chooser__selection-preview').text('Selection: ' + replacedUrl);
                }
            });
        });

        // Media Chooser select
        $body.on('click', '.js-url-chooser-media-select', function (e) {
            e.preventDefault();

            var $this = $(this),
                path = $this.data('path'),
                thumbPath = $this.data('thumb-path'),
                id = $this.data('id'),
                title = $this.data('title'),
                cke = $this.data('cke'),
                replaceUrl = $this.closest('.thumbnail-wrapper').data('replace-url');

            // Store values
            itemUrl = path;
            itemId = id;
            itemTitle = title;
            itemThumbPath = thumbPath;

            // Save
            if (!cke) {
                var isMediaChooser = $(window.frameElement).closest('.js-ajax-modal').data('media-chooser');

                if (isMediaChooser) {
                    saveMediaChooserModal(false);
                } else {
                    // Replace URL
                    $.ajax({
                        url: replaceUrl,
                        type: 'GET',
                        data: {'text': itemUrl},
                        success: function (response) {
                            replacedUrl = response.text;
                        }
                    }).done(function () {
                        saveUrlChooserModal(false);
                    });
                }

            } else {
                saveMediaChooserModal(true);
            }
        });


        // Cancel
        $('#cancel-url-chooser-modal').on('click', function () {
            var cke = $(this).data('cke');

            if (!cke) {
                var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                    parentModalId = $parentModal.attr('id');

                parent.$('#' + parentModalId).modal('hide');

            } else {
                window.close();
            }
        });


        // OK
        $(document).on('click', '#save-url-chooser-modal', function () {
            var cke = $(this).data('cke');

            saveUrlChooserModal(cke);
        });
    };


    // Save for URL-chooser
    saveUrlChooserModal = function (cke) {
        if (!cke) {
            var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                linkedInputId = $parentModal.data('linked-input-id'),
                parentModalId = $parentModal.attr('id');

            // Set val
            parent.$('#' + linkedInputId).val(itemUrl).change();

            // Set proper URL
            parent.$('#' + linkedInputId).parent().find('.js-urlchooser-value').val(replacedUrl);

            // Close modal
            parent.$('#' + parentModalId).modal('hide');

        } else {
            var funcNum = getUrlParam('CKEditorFuncNum');

            // Set val
            window.opener.CKEDITOR.tools.callFunction(funcNum, itemUrl);

            // Close window
            window.close();
        }
    };


    // Save for Media-chooser
    saveMediaChooserModal = function (cke) {
        if (!cke) {
            var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                linkedInputId = $parentModal.data('linked-input-id'),
                parentModalId = $parentModal.attr('id');

            // Set val
            parent.$('#' + linkedInputId).val(itemId).change();

            // Update preview
            var $mediaChooser = parent.$('#' + linkedInputId + '-widget'),
                $previewImg = parent.$('#' + linkedInputId + '__preview__img'),
                $previewTitle = parent.$('#' + linkedInputId + '__preview__title');

            $mediaChooser.addClass('media-chooser--choosen');
            $previewTitle.html(itemTitle);

            if (itemThumbPath === "") {
                var $parent = $previewTitle.parent();
                $parent.prepend('<i class="fa fa-file media-thumbnail__icon"></i>');
            }
            else {
                $previewImg.attr('src', itemThumbPath);
            }

            // Close modal
            parent.$('#' + parentModalId).modal('hide');

        } else {
            var funcNum = getUrlParam('CKEditorFuncNum');

            // Set val
            window.opener.CKEDITOR.tools.callFunction(funcNum, itemUrl);

            // Close window
            window.close();
        }
    };


    // Get Url Parameters
    getUrlParam = function (paramName) {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i'),
            match = window.location.search.match(reParam);

        return (match && match.length > 1) ? match[1] : '';
    };

    // Adapt the url chooser according to the selected link type.
    adaptUrlChooser = function () {
        $body.on('click', '.js-change-link-type', function (e) {
                e.preventDefault();
                var $form = $(this).closest('form'),
                    $urlChooser = $(this).parents('.urlchooser-wrapper'),
                    $urlChooserName = $urlChooser.data('chooser-name');

                var values = {};

                $.each($form.serializeArray(), function (i, field) {
                    // Only submit required values.
                    if (field.name.indexOf('link_type') !== -1 || field.name.indexOf('link_url') !== -1) {
                        if (field.name.indexOf($urlChooserName) !== -1 && field.name.indexOf('link_url') === -1) {
                            values[field.name] = field.value;
                        }
                    }
                    else {
                        // Main sequence can not be submitted.
                        if (field.name.indexOf('sequence') === -1) {
                            // handle array values
                            if (endsWith(field.name, '[]')) {
                                if (typeof values[field.name] === 'undefined' || typeof values[field.name] === 'string') {
                                    values[field.name] = [field.value];
                                } else {
                                    values[field.name].push(field.value);
                                }
                            } else {
                                values[field.name] = field.value;
                            }
                        }
                    }
                });

                // Add the selected li value.
                values[$(this).data('name')] = $(this).data('value');

                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method'),
                    data: values,
                    success: function (html) {
                        $urlChooser.replaceWith(
                            $(html).find('#' + $urlChooser.attr('id'))
                        );
                    }
                });
            }
        );
    };

    /* Polyfill String.prototype.endsWith() for IE */
    endsWith = function(string, search, this_len) {
        if (this_len === undefined || this_len > string.length) {
            this_len = string.length;
        }
        return string.substring(this_len - search.length, this_len) === search;
    };

    return {
        init: init
    };

})
(window);
