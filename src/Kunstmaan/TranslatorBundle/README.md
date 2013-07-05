# UNDER DEVELOPMENT!

# KunstmaanTranslatorBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanTranslatorBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanTranslatorBundle)

A bundle which enables editing translations in the admin interface without need for editing the translations files.
Translations will be stored in a (default) database and retrieved on the most efficient way possible.

It's possible to create your own stasher to store your translation files.

Installation requirements
-------------------------
You should be able to get Symfony >=2.3 up and running before you can install the KunstmaanTranslatorBundle.

Installation instructions
-------------------------
Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/translator-bundle
```

Add the KunstmaanTranslatorBundle to your AppKernel.php file:

```PHP
new Kunstmaan\Translator\KunstmaanTranslatorBundle(),
```

Add the KunstmaanTranslatorBundle to your routing.yml. Take your own routing into account, it's possible you will need to add the following code prior to your own routing configurations

```PHP
KunstmaanTranslatorBundle:
    resource: "@KunstmaanTranslatorBundle/Resources/config/routing.yml"
    prefix:   /
```

Overwrite the KunstmaanTranslatorBundle config to your needs in config.yml :

```PHP
kuma_translator:
    enable: true
```

Development instructions
-------------------------

Run unit tests

```bash
./vendor/bin/phpunit -c phpunit.xml.dist
```

Run PHP CS Fixer, after [installing php-cs-fixer system wide](https://github.com/fabpot/PHP-CS-Fixer#globally-manual)

```bash
php-cs-fixer fix .
```

Use
---

* [Documentation](https://github.com/Kunstmaan/KunstmaanVotingBundle/blob/master/Resources/doc/index.md)
