# KunstmaanTaggingBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanTaggingBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanTaggingBundle)

This bundle adds tagging to your Kunstmaan Bundles project.

Installation requirements
-------------------------
You should be able to get Symfony 2.3 up and running before you can install the KunstmaanTaggingBundle.

Installation instructions
-------------------------
Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/tagging-bundle
$ composer require fpn/doctrine-extensions-taggable
```

Add the KunstmaanTaggingBundle to your AppKernel.php file:

```
new Kunstmaan\TaggingBundle\KunstmaanTaggingBundle(),
```

You can find more detailed information on how to use this bundle [here](https://github.com/Kunstmaan/KunstmaanTaggingBundle/blob/master/Resources/doc/TaggingBundle.md)

## Symfony 2.2

If you want to use this bundle for a Symfony 2.2 release, use the 2.2 branch.