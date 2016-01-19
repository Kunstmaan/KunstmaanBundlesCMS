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


## The deprecated `security.context` service was replaced

The `security.context` service was replaced with the `security.token_storage` and `security.authorization_checker` service.
More information about this change: http://symfony.com/blog/new-in-symfony-2-6-security-component-improvements

You will only need to make changes when your code extends some functionality of the CMS that used the `security.context` service.

