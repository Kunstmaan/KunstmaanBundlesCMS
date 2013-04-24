# KunstmaanArticleBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanArticleBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanArticleBundle)

This bundle adds articles to your Kunstmaan Bundles project.

Installation requirements
-------------------------
You should be able to get Symfony 2.1 or greater up and running before you can install the KunstmaanArticleBundle.

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