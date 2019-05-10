var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.sidebartree = (function($, window, undefined) {

    var init,
        canBeMoved,
        buildTree, searchTree,
        $sidebar = $('#app__sidebar'),
        $sidebarNavContainer = $('#app__sidebar__navigation'),
        $searchField = $('#app__sidebar__search'),
        movingConfirmation = $sidebarNavContainer.data('moving-confirmation') || "You sure?";

    init = function() {
        if($sidebarNavContainer !== 'undefined' && $sidebarNavContainer !== null) {
            buildTree();
            searchTree();
        }
    };

    canBeMoved = function (node, parent) {
        if (!node.data.page || !node.data.page.class || !parent.data.page || !parent.data.page.children) {
            return false;
        }


        for (var i = parent.data.page.children.length, e; e = parent.data.page.children[--i]; ) {
            if (e.class === node.data.page.class) {
                return true;
            }
        }

        return false;

    };

    buildTree = function() {

        // Show when ready
        $sidebarNavContainer.on('ready.jstree', function() {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=ready.jstree
            $sidebar.addClass('app__sidebar--tree-ready');
        });


        // Go to url
        $sidebarNavContainer.on('changed.jstree', function(e, data) {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=changed.jstree
            var href = data.event.currentTarget.href;

            if (data.event.ctrlKey || data.event.metaKey) {
                window.open(href);
            } else {
                document.location.href = href;
            }
        });


        // Drag and drop callback
        $sidebarNavContainer.on('move_node.jstree', function(e, data) {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=move_node.jstree

            // Vars
            var $container = $(this),
                parentNode = data.parent,
                reorderUrl = $container.data('reorder-url'),
                params = {
                    nodes : []
                };

            // Recalc id's
            $('#' + parentNode).find('> ul > li').each(function() {
                var newId = $(this).attr('id').replace(/node-/,'');

                params.nodes.push(newId);
            });

            if (data.old_parent !== data.parent) {
                params.parent = {};
                params.parent[data.node.id.replace(/node-/, '')] = data.parent.replace(/node-/, '');
                if (0 === params.nodes.length) {
                    params.nodes.push(data.node.id.replace(/node-/, ''));
                }
            }

            //; Save
            $.post(
                reorderUrl,
                params,
                function(){
                    console.log('move_node saved');
                }
            );
        });


        // Create
        $sidebarNavContainer.jstree({
            'core': {
                'check_callback': function (operation, node, node_parent, node_position, more) {
                    // operation can be 'create_node', 'rename_node', 'delete_node', 'move_node' or 'copy_node'
                    // in case of 'rename_node' node_position is filled with the new node name

                    if(operation === 'move_node') {

                        // No dnd outsite root
                        if(!node_parent || node_parent.id === '#') {
                            return false;
                        }

                        // Only on same level please
                        if(node.parent === node_parent.id) {
                            return true;
                        }

                        return canBeMoved(node, node_parent) && !(more && more.core && !confirm(movingConfirmation
                            .replace('%title%', node.text.replace(/^\s+|\s+$/g, ''))
                            .replace('%target%', node_parent.text.replace(/^\s+|\s+$/g, ''))
                        ));

                    } else {

                        return true;
                    }
                }
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
                    'icon': 'fa fa-file'
                },
                'offline': {
                    'icon': 'fa fa-chain-broken'
                },
                'hidden-from-nav': {
                    'icon': 'fa fa-eye-slash'
                },
                'folder': {
                    'icon': 'fa fa-folder'
                },
                'image': {
                    'icon': 'fa fa-image'
                },
                'files': {
                    'icon': 'fa fa-copy'
                },
                'slideshow': {
                    'icon': 'fa fa-desktop'
                },
                'video': {
                    'icon': 'fa fa-film'
                },
                'media': {
                    'icon': 'fa fa-folder'
                }
            },
            'search' : {
                'show_only_matches' : true
            }
        });
    };

    searchTree = function() {

        if($searchField !== 'undefined' && $searchField !== null) {

            var options = {
                callback: function (value) { $sidebarNavContainer.jstree(true).search(value); },
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
