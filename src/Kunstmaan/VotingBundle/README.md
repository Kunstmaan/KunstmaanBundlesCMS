KunstmaanVotingBundle by Kunstmaan
=================================

About
-----
The KunstmaanVotingBundle is a plugin to add voting to your Symfony2 project.

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

```
new Kunstmaan\VotingBundle\KunstmaanVotingBundle(),
```

Use
---

### Facebook

Make sure the Facebook plugin javascript has been added to your page. 

See : https://developers.facebook.com/docs/reference/javascript/

#### Facebook Like

Add the following code to your template. The 'value' parameter is optional. Default value is set to 1.

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' with {'value' : value} %}
```

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanVotingBundle
```
