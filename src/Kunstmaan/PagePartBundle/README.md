# About the KunstmaanPagePartBundle

The [KunstmaanPagePartBundle](https://github.com/Kunstmaan/KunstmaanPagePartBundle) forms the basis of our content management
framework. A page built using a composition of "blocks" names pageparts. These pageparts allow you to fully separate the
data from the presentation so non-technical webmasters can manage the website. Every page can have it's own list of possible pageparts,
and pageparts are easy to create for your specific project to allow for rapid development.

View screenshots and more on the [github page](http://kunstmaan.github.com/KunstmaanPagePartBundle)

# Versions

We use the [Semantic Versioning scheme](http://semver.org) for our bundles, coupled with branches so we can distribute
bugfixes.

## [master](https://github.com/Kunstmaan/KunstmaanPagePartBundle)

Contains the latest code. Will most likely have bc breaks and possibly will not be stable enough for production. It is
available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/pagepart-bundle) and is tested on
Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanPagePartBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanPagePartBundle)

Installation instructions
-------------------------
Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/pagepart-bundle
```

Add the KunstmaanPagePartBundle to your AppKernel.php file:

```
new Kunstmaan\PagePartBundle\KunstmaanPagePartBundle(),
```

## [1.0 branch](https://github.com/Kunstmaan/KunstmaanPagePartBundle/tree/1.0)

Contains the first version that we are running in production. It is not advisable to run this in a new project. We will
continue to support the 1.0 branch and keep releasing 1.0.x versions as long as we have a production website operating
on this major version. It is available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/pagepart-bundle)
and is tested on Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanPagePartBundle.png?branch=1.0)](http://travis-ci.org/Kunstmaan/KunstmaanPagePartBundle)

## [1.1 branch](https://github.com/Kunstmaan/KunstmaanPagePartBundle/tree/1.1)

Contains the first version that we are running in production. It is not advisable to run this in a new project. We will
continue to support the 1.1 branch and keep releasing 1.1.x versions as long as we have a production website operating
on this major version. It is available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/pagepart-bundle)
and is tested on Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanPagePartBundle.png?branch=1.1)](http://travis-ci.org/Kunstmaan/KunstmaanPagePartBundle)

## [1.2 branch](https://github.com/Kunstmaan/KunstmaanPagePartBundle/tree/1.2)

The current stable version. It still has some issues but is stable for the most part. We will continue to support the
1.2 branch and keep releasing 1.2.x versions as long as we have a production website operating on this major version.
It is wise to always use the latest version in this branch since it will not have bc breaks and will fix bugs. It's
available on GitHub and on [Packagist](http://packagist.org/packages/kunstmaan/pagepart-bundle) and is tested on
Travis CI [![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanPagePartBundle.png?branch=1.2)](http://travis-ci.org/Kunstmaan/KunstmaanPagePartBundle)

### Installation
#### Using deps and vendor install

Add the following lines to your deps file:

```ini
[KunstmaanAdminBundle]
    git=git@github.com:Kunstmaan/KunstmaanAdminBundle.git
    target=/bundles/Kunstmaan/AdminBundle
    version=origin/1.2
[KunstmaanPagePartBundle]
    git=git@github.com:Kunstmaan/KunstmaanPagePartBundle.git
    target=/bundles/Kunstmaan/PagePartBundle
    version=origin/1.2
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
        "kunstmaan/pagepart-bundle": "1.2.*"
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
        // ...
    );
}
```

# Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Kunstmaan/KunstmaanSearchBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project built using the Symfony Standard Edition
to allow developers of the bundle to reproduce the issue by simply cloning it and following some steps.
