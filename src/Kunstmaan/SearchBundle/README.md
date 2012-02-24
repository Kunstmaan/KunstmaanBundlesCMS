KunstmaanSearchBundle by Kunstmaan
=================================

About
-----
The KunstmaanSearchBundle for Symfony 2 is part of the bundles we use to build custom and flexible applications at Kunstmaan.
You have to install this bundle in order to be able to add search and indexing functions.

View screenshots and more on our [github page](http://kunstmaan.github.com/KunstmaanSearchBundle)

[![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanSearchBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanSearchBundle)

Installation requirements
-------------------------
You should be able to get Symfony 2 up and running before you can install the KunstmaanSearchBundle.

Installation instructions
-------------------------
Installation is straightforward, add the following lines to your deps file:

```
[KunstmaanSearchBundle]
    git=https://github.com/Kunstmaan/KunstmaanSearchBundle.git
    target=/bundles/Kunstmaan/SearchBundle
```

Register the Kunstmaan namespace in your autoload.php file:

```
'Kunstmaan'        => __DIR__.'/../vendor/bundles'
```

Add the KunstmaanSearchBundle to your AppKernel.php file:

```
new Kunstmaan\SearchBundle\KunstmaanSearchBundle(),
```

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanSearchBundle
```