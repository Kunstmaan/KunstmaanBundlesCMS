# KunstmaanArticleBundle

[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanArticleBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanArticleBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/article-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/article-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/article-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/article-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanArticleBundle)](https://github.com/igrigorik/ga-beacon)

This bundle adds articles to your Kunstmaan Bundles project.

Installation requirements
-------------------------
You should be able to get Symfony 3 or greater up and running before you can install the KunstmaanArticleBundle.

Installation instructions
-------------------------
Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/article-bundle
```

Add the KunstmaanArticleBundle to your AppKernel.php file:

```
new Kunstmaan\ArticleBundle\KunstmaanArticleBundle(),
```

You can find more detailed information on how to use this bundle [here](https://github.com/Kunstmaan/KunstmaanArticleBundle/blob/master/Resources/doc/ArticleBundle.md).

Generate
--------

This bundle has been developed to work closely with the [KunstmaanGeneratorBundle](https://github.com/Kunstmaan/KunstmaanGeneratorBundle). This bundle contains abstract classes used by the generated article classes. For more information, please read the extended documentation on the [Article generator](https://github.com/Kunstmaan/KunstmaanGeneratorBundle).

## Symfony 2.2

If you want to use this bundle for a Symfony 2.2 release, use the 2.2 branch.
