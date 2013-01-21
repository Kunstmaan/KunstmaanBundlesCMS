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

Generate a Bundle :

```
app/console kuma:generate:bundle
```

Generate a default website using the Kunstmaan bundles :

```
app/console kuma:generate:default-site --namespace=Namespace\NamedBundle --prefix=tableprefix_
```

Generate a KunstmaanAdminList for an Entity :

```
app/console kuma:generate:adminlist --entity=Bundle:Entity
```

Documentation
------------

You can find more detailed information about these commands [here](https://github.com/Kunstmaan/KunstmaanGeneratorBundle/blob/master/Resources/doc/index.md)

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanGeneratorBundle
```