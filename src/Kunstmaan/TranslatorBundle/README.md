# UNDER DEVELOPMENT!

# KunstmaanTranslatorBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanTranslatorBundle.png?branch=develop)](http://travis-ci.org/Kunstmaan/KunstmaanTranslatorBundle)

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
new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
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

Migrate dev translations to production
------------------------------------------

Note: only use this with a SQL stasher.

Use the following command to generate a doctrine migration with all new and updated translations in your environment.

```
app/console kuma:translator:migrations:diff
```

When you want to include these migrated translations into your environment use the normal doctrine migrate command.

```
app/console doctrine:migrations:migrate
```

Import existing translation files
-------------------------------------
When migrating your current project you can easily import the existing translation files.

Without parameters, all translations from the current `main` project will be included and all locales.
If you have already existing translations in the stasher with the same combination of 'domain', 'keyword', 'locale', non of them will be overwritten

```
app/console kuma:translator:import
```

To force overwrite the existing translations in the stasher:

```
app/console kuma:translator:import --force
```

To import translations from a specific bundle:
```
app/console kuma:translator:import --bundle=superCoolNewApplicationBundle
```

To import only specific locales:
```
app/console kuma:translator:import --locales=nl,fr,de
```

How does the caching works?
-------------------------------------

Translations are stored in a database, but cached when not running in debug mode.

```php
$kernel = new AppKernel('prod', false); // translations are cached an read from this cache
```

```php
$kernel = new AppKernel('dev', true); // translations are always loaded from the stash (slower, more queries)
```

When editing translations in the backend changes aren't immediately visible on your website.
The backend will show a warning message when not newer or updated translations aren't loaded into the cache.
Click on the `flush cache`button to rebuild the cache.


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
