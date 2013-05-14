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

## How to use

After installing this bundle, you can go to the '/en/sitemap' url on your website, a sitemap XML based on the [Sitemap protocol](http://www.sitemaps.org/protocol.html) will be generated.

You can hide pages from the sitemap by implementing the HiddenFromSitemap interface, this interface will allow you the hide the page and/or its children from the sitemap.