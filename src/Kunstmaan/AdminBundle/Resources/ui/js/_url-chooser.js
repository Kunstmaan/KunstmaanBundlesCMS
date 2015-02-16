var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.urlChooser = (function(window, undefined) {

    var init, urlChooser;


    init = function() {
        urlChooser();
    };


    // URL-Chooser
    urlChooser = function() {
        var $body = $('body'),
            itemUrl, itemId;

        // Link Chooser select
        $body.on('click', '.js-url-chooser-link-select', function(e) {
            e.preventDefault();

            var $this = $(this),
                slug = $this.data('slug'),
                id = $this.data('id');


            // Update preview
            $('#url-chooser__selection-preview').text('Selection: ' + slug);

            // Store values
            itemUrl = slug;
            itemId = id;
        });


        // Cancel
        $('#cancel-url-chooser-modal').on('click', function() {
            var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                parentModalId = $parentModal.attr('id');

            parent.$('#' + parentModalId).modal('hide');
        });


        // OK
        $('#save-url-chooser-modal').on('click', function() {
            var result = {
                path: itemUrl,
                id: itemId
            };

            var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                linkedInputId = $parentModal.data('linked-input-id'),
                parentModalId = $parentModal.attr('id');

            // Set val
            parent.$('#' + linkedInputId).val(itemUrl);

            // Close modal
            parent.$('#' + parentModalId).modal('hide');


            // OLD
            // function handleOK(result) {
            //     if (window.opener) {
            //         {% if cke %}
            //             var funcNum = getUrlParam('CKEditorFuncNum');
            //             window.opener.CKEDITOR.tools.callFunction(funcNum, result['path']);
            //         {% else %}
            //             window.opener.dialogWin.returnedValue = result;
            //             window.opener.dialogWin.returnFunc()
            //         {% endif %}
            //     } else {
            //         //alert("You have closed the main window.\n\nNo action will be taken on the choices in this dialog box.")
            //     }

            //     window.close();
            //     return false
            // }

            // function getUrlParam(paramName) {
            //     var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
            //     var match = window.location.search.match(reParam) ;
            //     return (match && match.length > 1) ? match[1] : '' ;
            // }
        });


        // OLD
        // $(document).ready(function() {
        //     $('.choosebutton{{ id }}').on('click', function(ev) {
        //         ev.preventDefault();
        //         openDGDialog('{{ path('KunstmaanNodeBundle_selecturl') }}', 580, 500, function(param){
        //             var widget = jQuery('#{{ id }}_widget');
        //             widget.find('input').val(dialogWin.returnedValue.path);
        //         });
        //     });
        // });
    };


    return {
        init: init
    };

}(window));
