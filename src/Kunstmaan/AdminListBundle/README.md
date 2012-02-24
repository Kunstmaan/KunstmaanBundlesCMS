KunstmaanAdminListBundle by Kunstmaan
=================================

About
-----
The KunstmaanAdminListBundle for Symfony 2 is part of the bundles we use to build custom and flexible applications at Kunstmaan.
You have to install this bundle in order to work with lists in the administrator area.

View screenshots and more on our [github page](http://kunstmaan.github.com/KunstmaanAdminListBundle).

[![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanAdminListBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanAdminListBundle)


Installation requirements
-------------------------
You should be able to get Symfony 2 up and running before you can install the KunstmaanAdminListBundle.

Installation instructions
-------------------------
Installation is straightforward, add the following lines to your deps file:

```
[KunstmaanAdminListBundle]
    git=git@github.com:Kunstmaan/KunstmaanAdminListBundle.git
    target=/bundles/Kunstmaan/AdminListBundle
```

Register the Kunstmaan namespace in your autoload.php file:

```
'Kunstmaan'        => __DIR__.'/../vendor/bundles'
```

Add the KunstmaanAdminListBundle to your AppKernel.php file:

```
new Kunstmaan\AdminListBundle\KunstmaanAdminListBundle(),
```

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanAdminListBundle
```