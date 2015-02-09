var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.app = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.toggle.init();

        kunstmaanbundles.sidebartoggle.init();
        kunstmaanbundles.sidebartree.init();
        kunstmaanbundles.filter.init();
        kunstmaanbundles.sortableTable.init();
        kunstmaanbundles.pageTemplateEditor.init();
        kunstmaanbundles.checkIfEdited.init();
        kunstmaanbundles.preventDoubleClick.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    kunstmaanbundles.app.init();
});
