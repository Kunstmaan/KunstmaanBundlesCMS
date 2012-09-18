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
Installation is straightforward, add the following lines to your deps file:

```
[KunstmaanGeneratorBundle]
    git=https://github.com/Kunstmaan/KunstmaanGeneratorBundle.git
    target=/bundles/Kunstmaan/GeneratorBundle
```

Register the Kunstmaan namespace in your autoload.php file:

```
'Kunstmaan'        => __DIR__.'/../vendor/bundles',
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
app/console kuma:generate:default-site --bundle=Company\NamedBundle
```

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