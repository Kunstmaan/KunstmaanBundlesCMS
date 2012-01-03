# Introducing [KunstmaanMediaBundle][KunstmaanMediaBundle]

De [KunstmaanMediaBundle][KunstmaanMediaBundle] is a symfony2 bundle used at [Kunstmaan][kunstmaan] to handle media, and media folders. It has objects to handle files, slides, videos and images and keeps them organised in folders.

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
            new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
            new Kunstmaan\MediaBundle\KunstmaanMediaBundle(),
            ...
        );
```

## Add to autoload.php:

```bash
$loader->registerNamespaces(array(
    ...
    'Imagine'          => __DIR__.'/../vendor/Imagine/lib',
    'Avalanche'        => __DIR__.'/../vendor/bundles',
    'Gaufrette'        => __DIR__.'/../vendor/gaufrette/src',
    'Kunstmaan'        => __DIR__.'/../vendor/bundles',
    ...
));
```

## Add to config.yml:

```bash
imports:
   KunstmaanMediaBundle:
        resource: @KunstmaanMediaBundle/Resources/config/config.yml

orm:
    entity_managers:
        default:
            mappings:
                ...
                KunstmaanMediaBundle: ~
```    

## Add to routing.yml:

```bash
KunstmaanMediaBundle:
    resource: "@KunstmaanMediaBundle/Resources/config/routing.yml"
    prefix:   /
    
_imagine:
    resource: .
    type:     imagine    
```    

## Add to parameters.ini:

```bash
[parameters]
    ...
    cdnpath="http://example.com/"

```    

       
[KunstmaanMediaBundle]: https://github.com/Kunstmaan/KunstmaanMediaBundle "KunstmaanMediaBundle"
[kunstmaan]: http://www.kunstmaan.be "Kunstmaan"                
