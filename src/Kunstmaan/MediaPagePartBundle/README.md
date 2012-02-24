KunstmaanPagePartBundle by Kunstmaan
=================================

About
-----
The KunstmaanPagePartBundle for Symfony 2 is part of the bundles we use to build custom and flexible applications at Kunstmaan.
You have to install this bundle in order to be able to add pageparts to nodes in the administrator interface.

Installation requirements
-------------------------
You should be able to get Symfony 2 up and running before you can install the KunstmaanPagePartBundle.

Installation instructions
-------------------------
Installation is straightforward, add the following lines to your deps file:

```
[KunstmaanPagePartBundle]
    git=git@github.com:Kunstmaan/KunstmaanPagePartBundle.git
    target=/bundles/Kunstmaan/PagePartBundle
```

Register the Kunstmaan namespace in your autoload.php file:

```
'Kunstmaan'        => __DIR__.'/../vendor/bundles'
```

Add the KunstmaanPagePartBundle to your AppKernel.php file:

```
new Kunstmaan\PagePartBundle\KunstmaanPagePartBundle(),
```

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanPagePartBundle
```