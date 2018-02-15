# UPGRADE FROM 5.0 to 5.1

## KunstmaanNodeSearchBundle

When deprecating the container access in version 6, we need to make sure we rewrite everything.

We created a service tag for the NodeSearcher class. Instead of getting the service from the container, 
you need to tag it with "kunstmaan_node_search.node_searcher" if you created a custom node searcher.
