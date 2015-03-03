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

            // Show when ready
            $sidebarNavContainer.on('ready.jstree', function() {
                // http://www.jstree.com/api/#/?q=.jstree%20Event&f=ready.jstree

                $sidebar.addClass('app__sidebar--tree-ready');
            });


            // Go to url
            $sidebarNavContainer.on('changed.jstree', function(e, data) {
                // http://www.jstree.com/api/#/?q=.jstree%20Event&f=changed.jstree

                var href = data.event.currentTarget.href;

                document.location.href = href;
            });


            // Drag and drop callback
            $sidebarNavContainer.on('move_node.jstree', function(e, data) {
                // http://www.jstree.com/api/#/?q=.jstree%20Event&f=move_node.jstree

                // Vars
                var $container = $(this),
                    parentNode = data.parent,
                    reorderUrl = $container.data('reorder-url'),
                    params = {
                        nodes : []
                    };

                // Reset id's
                $('#' + parentNode).find(' > ul > li').each(function() {
                    var id = $(this).attr('id').replace(/node-/,'');

                    params.nodes.push(id);
                });

                // Save
                $.post(
                    reorderUrl,
                    params,
                    function(result){
                        console.log('move_node saved');
                    }
                );
            });


            // Create
            $sidebarNavContainer.jstree({
                'core': {
                    'check_callback' : true
                },
                'plugins': [
                    'types',
                    'search',
                    'dnd'
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
