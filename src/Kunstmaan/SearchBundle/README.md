# About the KunstmaanSearchBundle

The KunstmaanSearchBundle works with ElasticSearch and supports different search providers. We currently support [Sherlock](https://github.com/polyfractal/sherlock) as a provider.

* Add your own objects to index using a tagged service which implements the [SearchConfigurationInterface](https://github.com/Kunstmaan/KunstmaanSearchBundle/blob/sherlock/Configuration/SearchConfigurationInterface.php)
* Want to add another search provider ? It's easy, just add a tagged service which implements the [SearchProviderInterface](https://github.com/Kunstmaan/KunstmaanSearchBundle/blob/sherlock/Search/SearchProviderInterface.php)

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

 searchindexprefix: prefix_


## Documentation

Further documentation on how to use and extend this bundle can be found [here](https://github.com/Kunstmaan/KunstmaanSearchBundle/tree/sherlock/Resources/doc/SearchBundle.md).

## Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Kunstmaan/KunstmaanSearchBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project built using the Symfony Standard Edition
to allow developers of the bundle to reproduce the issue by simply cloning it and following some steps.
