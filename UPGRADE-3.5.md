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


## Upgrade LiipImagineBundle from v0.20.2 to v1.4.3

It is not possible anymore to change the format of cached images with all versions that were released after v0.20.2 (see 
https://github.com/liip/LiipImagineBundle/issues/584). There is an issue on the LiipImagineBundle roadmap to fix this, 
but it will not be ready before the 2.0 release (see https://github.com/liip/LiipImagineBundle/issues/686). In the 
meanwhile we extended some services to implement a quick workaround so we can a least update the bundle to a recent
version. 

You should change the `liip_imagine` configuration and the routing when updating:

In your `config.yml` replace

```
liip_imagine:
    cache_prefix: uploads/cache
    driver: imagick
    data_loader: filesystem
    data_root: %kernel.root_dir%/../web
    formats : ['jpg', 'jpeg','png', 'gif', 'bmp']
```

with

```
liip_imagine:
    resolvers:
        default:
            web_path:
                cache_prefix: uploads/cache
    driver: imagick
    data_loader: default
```

and in your `routing.yml`

```
_imagine:
    resource: .
    type:     imagine
```

with

```
_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"
```


## getRequest() is marked as deprecated since version 2.4

In the AdminListController actions we have removed the default value for the request attribute. This needs to be passed
from the correct AbstractAdminListController.

