var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.urlchoosertree = (function($, window, undefined) {

    var init,
        buildTree, searchTree,
        $sidebar = $('#app__urlchooser'),
        $urlChooserContainer = $('#app__urlchooser__navigation'),
        $fetchUrl = $urlChooserContainer.data('src'),
        $searchField = $('#app__urlchooser__search');

    init = function() {
        if($urlChooserContainer !== 'undefined' && $urlChooserContainer !== null) {
            buildTree();
            searchTree();
        }
    };

    buildTree = function() {

        // Show when ready
        $urlChooserContainer.on('ready.jstree', function() {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=ready.jstree
            $sidebar.addClass('app__sidebar--tree-ready');
        });


        // Go to url
        $urlChooserContainer.on('changed.jstree', function(e, data) {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=changed.jstree
            var href = data.event.currentTarget.href;

            if (data.event.ctrlKey || data.event.metaKey) {
                window.open(href);
            } else {
                document.location.href = href;
            }
        });

        // Create
        $urlChooserContainer.jstree({
            'core' : {
                'data' : {
                    "url" : $fetchUrl,
                    "dataType" : "json", // needed only if you do not supply JSON headers
                    "data" : function (node) {
                        return { "id" : node.id };
                    }
                }
            },
            'plugins': [
                'types',
                'search'
            ],
            'types': {
                '#': {
                    'icon': 'fa fa-home'
                },
                'default': {
                    'icon' : 'fa fa-file-o'
                },
                'offline': {
                    'icon': 'fa fa-chain-broken'
                },
                'folder': {
                    'icon': 'fa fa-folder-o'
                },
                'image': {
                    'icon': 'fa fa-picture-o'
                },
                'files': {
                    'icon': 'fa fa-files-o'
                },
                'slideshow': {
                    'icon': 'fa fa-desktop'
                },
                'video': {
                    'icon': 'fa fa-film'
                },
                'media': {
                    'icon': 'fa fa-folder-o'
                }
            },
            'search' : {
                'show_only_matches' : true,
                'ajax' : {
                    'url': $fetchUrl + "_search",
                    'dataType': 'json'
                }
            }
        });
    };

    searchTree = function() {

        if($searchField !== 'undefined' && $searchField !== null) {

            var options = {
                callback: function (value) { $urlChooserContainer.jstree(true).search(value); },
                wait: 750,
                highlight: false,
                allowSubmit: false,
                captureLength: 2
            };

            $searchField.typeWatch( options );
        }
    };

    return {
        init: init
    };

})(jQuery, window);
