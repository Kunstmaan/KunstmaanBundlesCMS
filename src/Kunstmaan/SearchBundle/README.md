# About the KunstmaanSearchBundle

[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanSearchBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanSearchBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/search-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/search-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/search-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/search-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanSearchBundle)](https://github.com/igrigorik/ga-beacon)

The KunstmaanSearchBundle works with [ElasticSearch](http://www.elasticsearch.org/) and supports different search providers. The bundle currently supports [Sherlock](https://github.com/polyfractal/sherlock) as a provider.

* Add your own objects to index using a tagged service which implements the [SearchConfigurationInterface]()
* Want to add another search provider ? It's easy, just add a tagged service which implements the [SearchProviderInterface]()

More about these features can be found in our bundle [documentation](https://github.com/Kunstmaan/KunstmaanSearchBundle/blob/master/Resources/doc/SearchBundle.md)

Make sure you run ElasticSearch. You can download it [here](http://www.elasticsearch.org/download/) and extract the files. Then run the executable 'elasticsearch' in the bin directory.

## Installation

composer.json
```json
    "require": {
        "kunstmaan/search-bundle": "*"
    },
```

AppKernel.php:
```php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Kunstmaan\SearchBundle\KunstmaanSearchBundle(),
            // ...
        );
```

## Configuration

### Parameter

Add the following parameter to your parameters file (ini, yml, ...), the prefix will be automatically used before each indexname you specify.
```yaml
 searchindexprefix: prefix_
```

## Documentation

Further documentation on how to use and extend this bundle can be found [here](https://github.com/Kunstmaan/KunstmaanSearchBundle/tree/master/Resources/doc/SearchBundle.md).

## Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Kunstmaan/KunstmaanSearchBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project built using the Symfony Standard Edition
to allow developers of the bundle to reproduce the issue by simply cloning it and following some steps.
