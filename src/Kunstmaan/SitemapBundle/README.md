# KunstmaanSitemapBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanSitemapBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanSitemapBundle)

The KunstmaanSitemapBundle adds a sitemap to your website. it will generate a sitemap recursively from all the children of the top nodes.

## Installation instructions

composer.json
```json
    "require": {
        "kunstmaan/sitemap-bundle": "*"
    },
```

AppKernel.php:
```php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Kunstmaan\SitemapBundle\KunstmaanSitemapBundle(),
            // ...
        );
```

routing.yml
```
# KunstmaanSitemapBundle
KunstmaanSitemapBundle:
    resource: "@KunstmaanSitemapBundle/Resources/config/routing.yml"
    prefix:   /{_locale}/
    requirements:
        _locale: %requiredlocales%

```
## Documentation

The bundle comes with a generated XML and its own sitemap page you can add to your website, for more information, check our the more detailed [documentation](https://github.com/Kunstmaan/KunstmaanSitemapBundle/blob/master/Resources/doc/SitemapBundle.md).
