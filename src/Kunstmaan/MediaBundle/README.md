# Introducing [KunstmaanMediaBundle][KunstmaanMediaBundle]

De [KunstmaanMediaBundle][KunstmaanMediaBundle] is a symfony2 bundle used at [Kunstmaan][kunstmaan] to handle media, and media galleries. It has objects to handle files, slides, videos and images and keeps them organised in galleries.

# Installing [KunstmaanMediaBundle][KunstmaanMediaBundle]

## Add to your deps file:

```bash
[KunstmaanMediaBundle]
    git=git@github.com:Kunstmaan/KunstmaanMediaBundle.git
    target=bundles/Kunstmaan/MediaBundle
```

## Dependencies:

```bash
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
```

## Add to appkernel.php:

```bash
$bundles = array(
            ...
            new Kunstmaan\MediaBundle\KunstmaanMediaBundle(),
            ...
        );
```

## Add to autoload.php:

```bash
$loader->registerNamespaces(array(
    ...
    'Kunstmaan'        => __DIR__.'/../vendor/bundles',
    ...
));
```

## Add to config.yml:

```bash
imports:
   KunstmaanKMediaBundle:
        resource: @KunstmaanMediaBundle/Resources/config/config.yml

orm:
    entity_managers:
        default:
            mappings:
                ...
                KunstmaanMediaBundle: ~
```       
       
[KunstmaanMediaBundle]: https://github.com/Kunstmaan/KunstmaanMediaBundle "KunstmaanMediaBundle"
[kunstmaan]: http://www.kunstmaan.be "Kunstmaan"                