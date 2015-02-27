var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.sidebartree = (function($, window, undefined) {

    var init,
        buildTree, searchTree,
        $sidebar = $('#app__sidebar'),
        $sidebarNavContainer = $('#app__sidebar__navigation'),
        $searchField = $('#app__sidebar__search');

    init = function() {
        buildTree();
        searchTree();
    };

    buildTree = function() {
        if($sidebarNavContainer !== 'undefined' && $sidebarNavContainer !== null) {
            $sidebarNavContainer
            // Show when ready
            .on('ready.jstree', function() {
                $sidebar.addClass('app__sidebar--tree-ready');
            })
            .on('changed.jstree', function(e, data) {
                var href = data.event.currentTarget.href;

                document.location.href = href;
            })
            // Create
            .jstree({
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
                    'show_only_matches': true
                }
            });
        }
    };

    searchTree = function() {
        if($searchField !== 'undefined' && $searchField !== null) {
            $searchField.on('keyup', function() {
                var searchValue = $searchField.val();

                $sidebarNavContainer.jstree(true).search(searchValue);
            });
        }
    };

    return {
        init: init
    };

}(jQuery, window));
