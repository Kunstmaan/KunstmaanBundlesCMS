# KunstmaanSitemapBundle

[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanSitemapBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanSitemapBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/sitemap-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/sitemap-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/sitemap-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/sitemap-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanSitemapBundle)](https://github.com/igrigorik/ga-beacon)

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
        _locale: "%requiredlocales%"

```

## Use

Once installed, you will be able to view the generated sitemap XML on the '/en/sitemap.xml' route.

## Documentation

The bundle comes with a generated XML and its own sitemap page you can add to your website, for more information, check our the more detailed [documentation](https://github.com/Kunstmaan/KunstmaanSitemapBundle/blob/master/Resources/doc/SitemapBundle.md).
