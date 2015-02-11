var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.richEditor = (function(window, undefined) {

    var init,
        setUp;


    init = function() {

        $('.js-rich-editor').each(function() {
            setUp($(this));
        });

    };


    setUp = function($el) {
        var $body = $('body'),
            fileBrowseUrl = $body.data('file-browse-url'),
            imageBrowseUrl = $body.data('image-browse-url');


        CKEDITOR.replace($el.attr('id'), {
            skin: 'bootstrapck',
            startupFocus: false,
            height: 500,
            bodyClass: 'CKEditor',

            filebrowserBrowseUrl: fileBrowseUrl,
            filebrowserWindowWidth: 580,

            filebrowserImageBrowseUrl: imageBrowseUrl,
            filebrowserImageBrowseLinkUrl: imageBrowseUrl,
            filebrowserImageWindowWidth: 970,

            filebrowserImageUploadUrl: '',

            toolbar: [
                {
                    name: 'basicstyles',
                    items : ['Bold','Italic','Underline','Strike','Subscript','Superscript', 'RemoveFormat']
                },
                {
                    name: 'lists',
                    items : ['NumberedList','BulletedList']
                },
                {
                    name: 'dents',
                    items : ['Outdent','Indent']
                },
                {
                    name: 'links',
                    items : ['Link','Unlink', 'Anchor']
                },
                {
                    name: 'insert',
                    items : ['Image', 'SpecialChar']
                },
                {
                    name: 'clipboard',
                    items : ['SelectAll', 'Cut','Copy','PasteText','PasteFromWord','-','Undo','Redo']
                },
                {
                    name: 'editing',
                    items : []
                },
                {
                    name: 'document',
                    items : [ 'Source' ]
                }
            ]
        });
    };





    // OLD -> ckeditor.js.twig
    // CKEDITOR.editorConfig = function( config )
    // {
    //     config.skin = 'bootstrapck';
    //     config.startupFocus = false;
    //     config.height = 500;
    //     config.bodyClass = 'CKEditor';
    //     {% if nodebundleisactive is defined %}
    //         config.filebrowserBrowseUrl = '{{ path('KunstmaanNodeBundle_ckselecturl') }}';
    //     {% else %}
    //         config.filebrowserBrowseUrl = '';
    //     {% endif %}
    //     config.filebrowserWindowWidth = '580';
    //     {% if mediabundleisactive is defined %}
    //         config.filebrowserImageBrowseUrl = '{{ path('KunstmaanMediaBundle_chooser', {'type': 'image'}) }}';
    //         config.filebrowserImageBrowseLinkUrl = '{{ path('KunstmaanMediaBundle_chooser', {'type': 'image'}) }}';
    //         config.filebrowserImageWindowWidth = '970';
    //     {% else %}
    //         config.filebrowserImageBrowseUrl = '';
    //         config.filebrowserImageBrowseLinkUrl = '';
    //     {% endif %}
    //     config.filebrowserImageUploadUrl = '';
    //     config.toolbar =
    //         [
    //             { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript', 'RemoveFormat' ] },
    //             { name: 'lists', items : [ 'NumberedList','BulletedList' ] },
    //             { name: 'dents', items : [ 'Outdent','Indent' ] },
    //             { name: 'links', items : [ 'Link','Unlink', 'Anchor' ] },
    //             { name: 'insert', items : [ 'Image', 'SpecialChar' ] },
    //             { name: 'clipboard', items : [ 'SelectAll', 'Cut','Copy','PasteText','PasteFromWord','-','Undo','Redo' ] },
    //             { name: 'editing', items : [  ] },
    //             { name: 'document', items : [ 'Source' ] }
    //         ];
    // };



    // OLD -> _js_footer.html.twig
    // jQuery(document).ready(function(){
    //     jQuery('textarea.rich_editor').each(function(item){
    //         var config = {};
    //         if($(this).attr('height')){
    //             config.height = $(this).attr('height');
    //         }
    //         if($(this).attr('noparagraphs')){
    //             config.enterMode = CKEDITOR.ENTER_BR;
    //             config.shiftEnterMode = CKEDITOR.ENTER_P;
    //         }
    //         CKEDITOR.replace( $(this).attr('id'), config);

    //         // Add id on iframe so that behat tests can interact
    //         var id = $(this).attr('id');
    //         var checkExist = setInterval(function() {
    //             if ($('.pagepart .cke_editor_' + id + ' iframe').length) {
    //                 var parts = id.split("_");
    //                 var name = parts[parts.length-1];
    //                 $('.pagepart .cke_editor_' + id + ' iframe').attr('id', 'cke_iframe_' + name);
    //                 clearInterval(checkExist);
    //             }
    //         }, 150);
    //     });
    // });



    return {
        init: init
    };

}(window));
