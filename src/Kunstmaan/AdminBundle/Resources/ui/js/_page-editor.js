var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pageEditor = (function(window, undefined) {

    var init,
    changeTemplate, publishLater, unpublishLater, sortable, permissions, keyCombinations;

    var $body = $('body');


    init = function() {
        $('.js-change-page-template').on('click', function() {
            changeTemplate($(this));
        });
        if($('#publish-later__check').length) {
            publishLater();
        }
        if($('#unpublish-later__check').length) {
            unpublishLater();
        }
        if($('.js-sortable-container').length) {
            sortable();
        };
        if($('#permissions-container').length) {
            permissions();
        }
        if($('#pageadminform').length) {
            keyCombinations();
        };
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
    publishLater = function() {
        var _toggle;

        _toggle = function(check) {
            if(check.checked) {
                $('#publish-later').show();
                $('#publish-later-action').show();
                $('#publish-action').hide();

            } else {
                $('#publish-later').hide();
                $('#publish-later-action').hide();
                $('#publish-action').show();
            }
        };

        if($('#publish-later__check')) {
            var check = document.getElementById('publish-later__check');

            _toggle(check);

            $(check).on('change', function() {
                _toggle(this);
            });
        }
    };


    // Unpublish
    unpublishLater = function() {
        var _toggle = function(check) {

            if(check.checked) {
                $('#unpublish-later').show();
                $('#unpublish-later-action').show();
                $('#unpublish-action').hide();
            } else {
                $('#unpublish-later').hide();
                $('#unpublish-later-action').hide();
                $('#unpublish-action').show();
            }
        };

        if($('#unpublish-later__check')) {
            var check = document.getElementById('unpublish-later__check');

            _toggle(check);

            $(check).on('change', function() {
                _toggle(this);
            });
        }
    };


    // Sortable
    sortable = function() {
        $('.js-sortable-container').each(function() {
            var $this = $(this),
                id = $this.attr('id'),
                el = document.getElementById(id);

            var sortable = Sortable.create(el, {
                draggable: '.js-sortable-item',
                handle: '.js-sortable-item__handle',
                ghostClass: 'sortable-item--ghost',

                group: {
                    name: 'pagepartRegion',
                    pull: true
                },

                animation: 100,

                scroll: true,
                scrollSensitivity: 300,
                scrollSpeed: 300,

                onStart: function(evt) {
                    var $el = $(evt.item),
                        elScope = $el.data('scope');

                    // Destroy rich editors inside dragged element
                    $el.find('.js-rich-editor').each(function() {
                        kunstmaanbundles.richEditor.destroySpecificRichEditor($(this));
                    });

                    // Add active class
                    $body.addClass('sortable-active');

                    // Check if drag is allowed
                    $('.js-sortable-container').on('dragover', function(e) {
                        var $element = $(this);

                        if ($element.data('scope')) {
                            var allowedPageParts = $element.data('scope').split(' ');

                            if(allowedPageParts.indexOf(elScope) > -1) {
                                $el.removeClass('sortable-item--error');
                            } else {
                                $el.addClass('sortable-item--error');
                            }
                        }

                    });
                },

                onEnd: function(evt) {
                    var $el = $(evt.item),
                        $PPcontainer = $el.parents('.js-pp-container'),
                        $contextUpdateField = $el.find('.pagepartadmin_field_updatecontextname'),
                        currentContext = $PPcontainer.data('context');

                    // Remove active class
                    $body.removeClass('sortable-active');

                    // Remove event listeners
                    $('.js-sortable-container').off('dragover');

                    // Set edited on true
                    kunstmaanbundles.checkIfEdited.edited();

                    // Update context name
                    $contextUpdateField.each(function() {
                        $(this).attr('name', currentContext + $(this).data('suffix'));
                    });

                    // Enable rich editors inside dragged element
                    $el.find('.js-rich-editor').each(function() {
                        kunstmaanbundles.richEditor.enableRichEditor($(this));
                    });
                }
            });
        });

        // Add active class
        $('.js-sortable-item__handle').on('mousedown', function() {
            $body.addClass('sortable-active');
        });

        // Remove active class
        $('.js-sortable-item__handle').on('mouseup', function() {
            $body.removeClass('sortable-active');
        });
    };


    // Permission
    permissions = function() {
        // Container
        var $permissionsContainer = $('#permissions-container');

        // Changes
        var changes = [];
            changes['add'] = [];
            changes['del'] = [];

        // Checkboxes
        $('.js-permission-checkbox').on('change', function() {
            var checkbox = this,
                $checkbox = $(checkbox),
                role = $checkbox.data('role'),
                permission = $checkbox.data('permission'),
                origValue = $checkbox.data('original-value');

            // Add/Remove change
            if (origValue == checkbox.checked) {
                // Remove change...
                var idx;

                if (origValue) {
                    idx = changes['del'].indexOf(role + '.' + permission);

                    if (idx != -1) {
                        changes['del'].splice(idx, 1);
                    }
                } else {
                    idx = changes['add'].indexOf(role + '.' + permission);

                    if (idx != -1) {
                        changes['add'].splice(idx, 1);
                    }
                }

            } else {
                // Add change
                if (checkbox.checked) {
                    changes['add'].push(role + '.' + permission);
                } else {
                    changes['del'].push(role + '.' + permission);
                }
            }


            // Add hidden fields
            var hiddenfieldsContainer = $("#permission-hidden-fields"),
                hiddenfields;

            if (changes['add'].length > 0) {
                for (var i=0; i<changes['add'].length; i++) {
                    var params = changes['add'][i].split('.');
                    hiddenfields = hiddenfields + '<input type="hidden" name="permission-hidden-fields[' + params[0] + '][ADD][]" value="' + params[1] + '">';
                }
            }

            if (changes['del'].length > 0) {
                for (var i=0; i<changes['del'].length; i++) {
                    var params = changes['del'][i].split('.');
                    hiddenfields = hiddenfields + '<input type="hidden" name="permission-hidden-fields[' + params[0] + '][DEL][]" value="' + params[1] + '">';
                }
            }

            hiddenfieldsContainer.html(hiddenfields);


            // Display changes in div?
            var isRecursive = $permissionsContainer.data('recursive');
            if(isRecursive) {
                var transPermsAdded = $permissionsContainer.data('trans-perms-added'),
                    transPermsRemoved = $permissionsContainer.data('trans-perms-removed');

                var $infoContainer= $('#permission-changes-info-container'),
                    modalHtml = '';

                // Additions
                if (changes['add'].length > 0) {
                    modalHtml = modalHtml + '<p>' + transPermsAdded;
                    modalHtml = modalHtml + '<ul>';

                    for (var i=0; i<changes['add'].length; i++) {
                        var params = changes['add'][i].split('.');

                        modalHtml = modalHtml + '<li><strong>' + params[0] + '</strong> : ' + params[1] + '</li>';
                    }

                    modalHtml = modalHtml + '</ul>';
                    modalHtml = modalHtml + '</p>';
                }

                // Deletions
                if (changes['del'].length > 0) {
                    modalHtml = modalHtml + '<p>' + transPermsRemoved;
                    modalHtml = modalHtml + '<ul>';

                    for (var i=0; i<changes['del'].length; i++) {
                        var params = changes['del'][i].split('.');

                        modalHtml = modalHtml + '<li><strong>' + params[0] + '</strong> : ' + params[1] + '</li>';
                    }

                    modalHtml = modalHtml + '</ul>';
                    modalHtml = modalHtml + '</p>';
                }

                // Setup info container
                if (modalHtml != '') {
                    $('#permission-changes-modal__body').html(modalHtml);
                    $infoContainer.removeClass('hidden');
                } else {
                    $infoContainer.addClass('hidden');
                    $('#apply-recursive').prop('checked', false);
                }
            }
        });
    };


    // Key Combinations
    keyCombinations = function() {
        $(document).on('keydown', function(e) {
            if((e.ctrlKey || e.metaKey) && e.which === 83) {
                e.preventDefault();

                kunstmaanbundles.appLoading.addLoading();

                $('#pageadminform').submit();
            };
        });
    };


    return {
        init: init
    };

}(window));
