var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.sidebartree = (function($, window, undefined) {

    var init,
        buildTree, searchTree,
        $sidebar = $('#app__sidebar'),
        $sidebarNavContainer = $('#app__sidebar__navigation'),
        $searchField = $('#app__sidebar__search');

    init = function() {
        if($sidebarNavContainer !== 'undefined' && $sidebarNavContainer !== null) {
            buildTree();
            searchTree();
        }
    };

    buildTree = function() {

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

            // Recalc id's
            $('#' + parentNode).find('> ul > li').each(function() {
                var newId = $(this).attr('id').replace(/node-/,'');

                console.log('reset id: ' + newId);

                params.nodes.push(newId);
            });

            console.log('parentNode: ' + parentNode);
            console.log('results:');
            console.log(params);

            //; Save
            $.post(
                reorderUrl,
                params,
                function(result){
                    console.log('move_node saved');
                    console.log(result);
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
                        console.log('-- move_node ---------------');

                        // Has it got a parent (no dnd outsite home)
                        console.log('node_parent.id: ' + node_parent.id);

                        if(!node_parent || node_parent.id === '#') {
                            return false;
                        }

                        // Go ahead
                        return true;


                        // OLD
                        // "check_move" : function (m) {
                        //     var p = this._get_parent(m.o);
                        //     if(!p) return false;
                        //     p = p == -1 ? this.get_container() : p;
                        //     if(p === m.np) return true;
                        //     if(p[0] && m.np[0] && p[0] === m.np[0]) return true;
                        //     return false;
                        // }

                        console.log('-----------------');

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
