# UPGRADE FROM 3.4 to 3.5

## `gedmo.listener.tree` service was removed from KunstmaanNodeBundle (BC breaking)

The service `gedmo.listener.tree` was removed from NodeBundle. To upgrade you need to place the service definition
in your application config:

```
# app/config/config.yml
services:
    gedmo.listener.tree:
        class: Gedmo\Tree\TreeListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ @annotation_reader ] ]
```
