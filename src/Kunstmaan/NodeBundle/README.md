KunstmaanViewBundle by Kunstmaan
=================================

About
-----
The KunstmaanViewBundle for Symfony 2 is part of the bundles we use to build custom and flexible applications at Kunstmaan.
You have to install this bundle in order to display the contents of nodes on the front of the website.

View screenshots and more on our [github page](http://kunstmaan.github.com/KunstmaanViewBundle)

[![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanViewBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanViewBundle)


Installation requirements
-------------------------
You should be able to get Symfony 2 up and running before you can install the KunstmaanViewBundle.

Installation instructions
-------------------------
Installation is straightforward, add the following lines to your deps file:

```
[KunstmaanViewBundle]
    git=https://github.com/Kunstmaan/KunstmaanViewBundle.git
    target=/bundles/Kunstmaan/ViewBundle
```

Register the Kunstmaan namespace in your autoload.php file:

```
'Kunstmaan'        => __DIR__.'/../vendor/bundles'
```

Add the KunstmaanViewBundle to your AppKernel.php file:

```
new Kunstmaan\ViewBundle\KunstmaanViewBundle(),
```

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanViewBundle
```