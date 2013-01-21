# About the KunstmaanMediaPagePartBundle

The [KunstmaanMediaPagePartBundle](https://github.com/Kunstmaan/KunstmaanMediaPagePartBundle) is a pagepart that can be
used on the [KunstmaanPagePartBundle](https://github.com/Kunstmaan/KunstmaanPagePartBundle). It is a separate bundle to
prevent a tight coupling between the [KunstmaanPagePartBundle](https://github.com/Kunstmaan/KunstmaanPagePartBundle) and
the [KunstmaanMediaBundle](https://github.com/Kunstmaan/KunstmaanMediaBundle).

View screenshots and more on the [github page](http://kunstmaan.github.com/KunstmaanMediaPagePartBundle)

# Versions

We use the [Semantic Versioning scheme](http://semver.org) for our bundles, coupled with branches so we can distribute
bugfixes.

## [master](https://github.com/Kunstmaan/KunstmaanMediaPagePartBundle)

Contains the latest code. Will most likely have bc breaks and possibly will not be stable enough for production. It is
available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/media-pagepart-bundle) and is tested on
Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanMediaPagePartBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanMediaPagePartBundle)

Installation instructions
-------------------------
Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/media-pagepart-bundle
```

Add the KunstmaanMediaPagePartBundle to your AppKernel.php file:

```
new Kunstmaan\AdminBundle\KunstmaanAdminBundle(),
new Kunstmaan\PagePartBundle\KunstmaanPagePartBundle(),
new Kunstmaan\MediaBundle\KunstmaanMediaBundle(),
new Kunstmaan\MediaPagePartBundle\KunstmaanMediaPagePartBundle(),
```

## [1.0 branch](https://github.com/Kunstmaan/KunstmaanMediaPagePartBundle/tree/1.0)

Contains the first version that we are running in production. It is not advisable to run this in a new project. We will
continue to support the 1.0 branch and keep releasing 1.0.x versions as long as we have a production website operating
on this major version. It is available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/media-pagepart-bundle)
and is tested on Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanMediaPagePartBundle.png?branch=1.0)](http://travis-ci.org/Kunstmaan/KunstmaanMediaPagePartBundle)

## [1.1 branch](https://github.com/Kunstmaan/KunstmaanMediaPagePartBundle/tree/1.1)

The current stable version. It still has some issues but is stable for the most part. We will continue to support the
1.1 branch and keep releasing 1.1.x versions as long as we have a production website operating on this major version.
It is wise to always use the latest version in this branch since it will not have bc breaks and will fix bugs. It's
available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/media-pagepart-bundle) and is tested on
Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanMediaPagePartBundle.png?branch=1.2)](http://travis-ci.org/Kunstmaan/KunstmaanMediaPagePartBundle)

### Installation
#### Using deps and vendor install

Add the following lines to your deps file:

```ini
[KunstmaanAdminBundle]
    git=git@github.com:Kunstmaan/KunstmaanAdminBundle.git
    target=/bundles/Kunstmaan/AdminBundle
    version=origin/1.1
[KunstmaanPagePartBundle]
    git=git@github.com:Kunstmaan/KunstmaanPagePartBundle.git
    target=/bundles/Kunstmaan/PagePartBundle
    version=origin/1.1
[KunstmaanMediaBundle]
    git=git@github.com:Kunstmaan/KunstmaanMediaBundle.git
    target=/bundles/Kunstmaan/MediaBundle
    version=origin/1.1
[KunstmaanMediaPagePartBundle]
    git=git@github.com:Kunstmaan/KunstmaanMediaPagePartBundle.git
    target=/bundles/Kunstmaan/MediaPagePartBundle
    version=origin/1.1
```

Register the namespaces in your autoload.php file:

```
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Kunstmaan'        => __DIR__.'/../vendor/bundles'
    // ...
));

```

#### Using [Composer](http://getcomposer.org)

```json
{
    "require": {
        // ...
        "kunstmaan/media-pagepart-bundle": "1.1.*"
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
        new Kunstmaan\AdminBundle\KunstmaanAdminBundle(),
        new Kunstmaan\PagePartBundle\KunstmaanPagePartBundle(),
        new Kunstmaan\PagePartBundle\KunstmaanMediaBundle(),
        new Kunstmaan\PagePartBundle\KunstmaanMediaPagePartBundle(),
        // ...
    );
}
```

# Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Kunstmaan/KunstmaanMediaPagePartBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project built using the Symfony Standard Edition
to allow developers of the bundle to reproduce the issue by simply cloning it and following some steps.
