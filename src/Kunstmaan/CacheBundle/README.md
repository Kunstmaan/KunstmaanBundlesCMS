#  Using the Cache bundle


## Installation

This bundle is compatible with all Symfony 3.* releases. More information about installing can be found in this line by line walkthrough of installing Symfony and all our bundles, please refer to the [Getting Started guide](http://bundles.kunstmaan.be/getting-started) and enjoy the full blown experience.

## Usage

This bundle allows you to do stuff with caching. For now it's a starting bundle with the possibility
to ban stuff from varnish.

### Configure bundle in config.yml

This bundle works with the fos http cache bundle. Therefore you need to add the following configuration, of course with your own varnish path.
```YAML
fos_http_cache:
    proxy_client:
        varnish:
            servers:
                - 127.0.0.1:6081
    cache_manager:
        enabled: true
    invalidation:
        enabled: true
```

### Add the kunstmaan_cache routing to your routing.yml

```YAML
# KunstmaanCacheBundle
KunstmaanCacheBundle:
    resource: "@KunstmaanCacheBundle/Resources/config/routing.yml"
    prefix:   /{_locale}/
    requirements:
        _locale: "%requiredlocales%"
```
    
### Result

When you browse to "Settings" in the main menu, you will see that there is a new menu item available with the label of "Varnish ban".
There you can add a path that you would like to ban from varnish. When you check the all domains option and you are using a multidomain website,
the path will be banned from all hosts of your multidomain.

On nodes you have a new menu actions to clear the cache for that node.