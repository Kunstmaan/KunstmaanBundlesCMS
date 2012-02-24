KunstmaanMediaBundle by Kunstmaan
=================================

About
-----
The KunstmaanMediaBundle for Symfony 2 is part of the bundles we use to build custom and flexible applications at Kunstmaan.
The KunstmaanMediaBundle handles various types of media for the KunstmaanAdminBundle on the fontend and administrator interface.

View screenshots and more on our [github page](http://kunstmaan.github.com/KunstmaanMediaBundle).

Installation requirements
-------------------------
You should be able to get Symfony 2 up and running before you can install the KunstmaanMediaBundle.

Installation instructions
-------------------------
Installation is straightforward, add the following lines to your deps file:

```
[KunstmaanMediaBundle]
    git=git@github.com:Kunstmaan/KunstmaanMediaBundle.git
    target=/bundles/Kunstmaan/MediaBundle
```

Register the Kunstmaan namespace in your autoload.php file:

```
'Kunstmaan'        => __DIR__.'/../vendor/bundles'
```

Add the KunstmaanMediaBundle to your AppKernel.php file:

```
new Kunstmaan\MediaBundle\KunstmaanMediaBundle(),
```

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanMediaBundle
```