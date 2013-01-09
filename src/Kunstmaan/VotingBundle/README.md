KunstmaanVotingBundle by Kunstmaan
=================================

About
-----
The KunstmaanVotingBundle allows you to add extensible voting support to your project

Installation requirements
-------------------------
You should be able to get Symfony 2.1 up and running before you can install the KunstmaanVotingBundle.

Installation instructions
-------------------------
Assuming you have installed composer.phar or composer binary:

``` bash
$ composer require kunstmaan/voting-bundle
```

Add the KunstmaanVotingBundle to your AppKernel.php file:

```PHP
new Kunstmaan\VotingBundle\KunstmaanVotingBundle(),
```

Add the KunstmaanVotingBundle to your routing.yml. Take your own routing into account, it's possible you will need to add the following code prior to your own routing configurations

```PHP
KunstmaanVotingBundle:
    resource: "@KunstmaanVotingBundle/Resources/config/routing.yml"
    prefix:   /
```

Use
---

* [Documentation](https://github.com/Kunstmaan/KunstmaanVotingBundle/blob/master/Resources/doc/index.md)

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanVotingBundle
```
