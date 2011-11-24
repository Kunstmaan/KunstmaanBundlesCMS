# Introducing [KunstmaanMediaBundle][KunstmaanMediaBundle]

[][kdeploy] is the hosting platform used at [Kunstmaan][kunstmaan] to reliably host, deploy, backup and develop our projects. There are no restrictions on what type of projects are hosted and we are using it to host projects based on Java in a Tomcat container, Play! with the builtin container, PHP using mod_apache and PHP-FPM and a Ruby On Rails application.

# Installing [kDeploy][kdeploy]

## Voeg toe aan deps:

[KunstmaanMediaBundle]
    git=git@github.com:Kunstmaan/KunstmaanMediaBundle.git
    target=bundles/Kunstmaan/MediaBundle

Dependencies:

[KunstmaanAdminBundle]
    git=
    target=

[Imagine]
    git=git://github.com/avalanche123/Imagine.git
    target=/Imagine

[AvalancheImagineBundle]
    git=http://github.com/avalanche123/AvalancheImagineBundle.git
    target=bundles/Avalanche/Bundle/ImagineBundle

[gaufrette]
    git=git://github.com/knplabs/Gaufrette.git
    target=/gaufrette

[KnpGaufretteBundle]
    git=http://github.com/knplabs/KnpGaufretteBundle.git
    target=/bundles/Knp/GaufretteBundle


Voeg toe aan appkernel.php:

```bash
$bundles = array(
            ...
            new Kunstmaan\MediaBundle\KunstmaanMediaBundle(),
            ...
        );
```

Voeg toe aan autoload.php:

$loader->registerNamespaces(array(
    ...
    'Kunstmaan'        => __DIR__.'/../vendor/bundles',
    ...
));


Voeg toe aan config.yml:

imports:
   KunstmaanKMediaBundle:
        resource: @KunstmaanMediaBundle/Resources/config/config.yml

orm:
    entity_managers:
        default:
            mappings:
                ...
                KunstmaanMediaBundle: ~