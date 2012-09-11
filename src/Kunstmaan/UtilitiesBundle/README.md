KunstmaanUtilitiesBundle by Kunstmaan ![image](http://travis-ci.org/Kunstmaan/KunstmaanUtilitiesBundle.png)
============================

About
-----
The KunstmaanUtilitiesBundle for Symfony 2 is part of the bundles we use to build custom and flexible applications at Kunstmaan.
This bundle will contain a set of helpful helper classes.

Installation requirements
-------------------------
You should be able to get Symfony 2 up and running before you can install the KunstmaanUtilitiesBundle.

Installation instructions
-------------------------
Installation is straightforward, add the following lines to your deps file:

```yaml
[KunstmaanUtilitiesBundle]
    git=https://github.com/Kunstmaan/KunstmaanUtilitiesBundle.git
    target=/bundles/Kunstmaan/UtilitiesBundle
```

Register the Kunstmaan namespace in your autoload.php file:

```php
'Kunstmaan'        => __DIR__.'/../vendor/bundles'
```

Add the KunstmaanUtilitiesBundle to your AppKernel.php file:

```php
new Kunstmaan\UtilitiesBundle\KunstmaanUtilitiesBundle(),
```

Utilities
---------

#Cipher
Cipher is a helpful service to encrypt and decrypt string values. To start using cypher you must first register the service:

```yaml
utilities.cipher:
    class: 'Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher'
    arguments: ['<YOUR_SECRET_HERE>']
```

More information can be found here in [Resources\doc\cipher.md](https://github.com/Kunstmaan/KunstmaanUtilitiesBundle/blob/master/Resources/doc/cipher.md)

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git@github.com:Kunstmaan/KunstmaanUtilitiesBundle.git
```
