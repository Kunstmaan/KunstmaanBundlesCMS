# About the KunstmaanSearchBundle

The [KunstmaanSearchBundle](https://github.com/Kunstmaan/KunstmaanSearchBundle) is the glue between the
[KunstmaanViewBundle](https://github.com/Kunstmaan/KunstmaanViewBundle) and the
[FOQElasticaBundle](https://github.com/Exercise/FOQElasticaBundle). It features automatic transformation of pages into
indexable objects for Elastica. It also provides a navigation item in the [KunstmaanAdminBundle](https://github.com/Kunstmaan/KunstmaanAdminBundle)
where it registers all the performed search queries for further evaluation.

View screenshots and more on the [github page](http://kunstmaan.github.com/KunstmaanSearchBundle)

# Versions

We use the [Semantic Versioning scheme](http://semver.org) for our bundles, coupled with branches so we can distribute
bugfixes.

## [master](https://github.com/Kunstmaan/KunstmaanSearchBundle)

Contains the latest code. Will most likely have bc breaks and possibly will not be stable enough for production. It is
available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/search-bundle) and is tested on
Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanSearchBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanSearchBundle)

### Installation
#### Using deps and vendor install

Add the following lines to your deps file:

```ini
[Elastica]
    git=git://github.com/ruflin/Elastica.git
    target=elastica
[FOQElasticaBundle]
    git=git://github.com/Exercise/FOQElasticaBundle.git
    target=bundles/FOQ/ElasticaBundle
    version=origin/2.0
[KunstmaanSearchBundle]
    git=https://github.com/Kunstmaan/KunstmaanSearchBundle.git
    target=/bundles/Kunstmaan/SearchBundle
```

Register the namespaces in your autoload.php file:

```
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Elastica'         => __DIR__.'/../vendor/elastica/lib',
    'FOQ'              => __DIR__.'/../vendor/bundles',
    'Kunstmaan'        => __DIR__.'/../vendor/bundles'
    // ...
));

```

#### Using [Composer](http://getcomposer.org)

```json
{
    "require": {
        // ...
        "kunstmaan/search-bundle": "dev-master"
        // ...
    }
}
```

## [1.0 branch](https://github.com/Kunstmaan/KunstmaanSearchBundle/tree/1.0)

Contains the first version that we are running in production. It still has some issues but is stable for the most part.
We will continue to support the 1.0 branch and keep releasing 1.0.x versions as long as we have a production website
operating on this major version. It is wise to always use the latest version in this branch since it will not have bc breaks
and will fix bugs. It is available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/search-bundle) and
is tested on Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanSearchBundle.png?branch=1.0)](http://travis-ci.org/Kunstmaan/KunstmaanSearchBundle)

### Installation
#### Using deps and vendor install

Add the following lines to your deps file:

```ini
[Elastica]
    git=git://github.com/ruflin/Elastica.git
    target=elastica
[FOQElasticaBundle]
    git=git://github.com/Exercise/FOQElasticaBundle.git
    target=bundles/FOQ/ElasticaBundle
    version=origin/2.0
[KunstmaanSearchBundle]
    git=https://github.com/Kunstmaan/KunstmaanSearchBundle.git
    target=/bundles/Kunstmaan/SearchBundle
    version=origin/1.0
```

Register the namespaces in your autoload.php file:

```
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Elastica'         => __DIR__.'/../vendor/elastica/lib',
    'FOQ'              => __DIR__.'/../vendor/bundles',
    'Kunstmaan'        => __DIR__.'/../vendor/bundles'
    // ...
));

```

#### Using [Composer](http://getcomposer.org)

```json
{
    "require": {
        // ...
        "kunstmaan/search-bundle": "1.0.*"
        // ...
    }
}
```

# Configuration

Add the bundles to your AppKernel.php file:

```php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        // ...
        new FOQ\ElasticaBundle\FOQElasticaBundle(),
        new Kunstmaan\SearchBundle\KunstmaanSearchBundle(),
        // ...
    );
}
```

Add the following settings to your parameters.ini

```ini
    ; app/config/parameters.ini

    searchport="9200"
    searchindexname="myindex"
```

And import the config into your config.yml

```
imports:
    KunstmaanSearchBundle:
        resource: @KunstmaanSearchBundle/Resources/config/config.yml
```

# Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Kunstmaan/KunstmaanSearchBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project built using the Symfony Standard Edition
to allow developers of the bundle to reproduce the issue by simply cloning it and following some steps.
