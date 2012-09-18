KunstmaanGeneratorBundle by Kunstmaan
=================================

About
-----
The KunstmaanGeneratorBundle for Symfony 2 is part of the bundles we use to build custom and flexible applications at Kunstmaan.
The KunstmaanGeneratorBundle is a feature bundle and supplies helpful generators for the Kunstmaan bundles.

View screenshots and more on our [github page](http://kunstmaan.github.com/KunstmaanGeneratorBundle).

[![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanGeneratorBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanGeneratorBundle)

Installation requirements
-------------------------
You should be able to get Symfony 2 up and running before you can install the KunstmaanGeneratorBundle.

Installation instructions
-------------------------
Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/generator-bundle
```

Add the KunstmaanGeneratorBundle to your AppKernel.php file:

```
$bundles[] = new Kunstmaan\GeneratorBundle\KunstmaanGeneratorBundle();
```

Use
---

Generate a KunstmaanAdminList for an Entity :

```
 app/console kuma:generate:adminlist --entity=Bundle:Entity
```

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanGeneratorBundle
```