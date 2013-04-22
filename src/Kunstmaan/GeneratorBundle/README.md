# KunstmaanGeneratorBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanGeneratorBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanGeneratorBundle)

If you're like us, you like to build applications without having to do the same things over and over again and dislike copy/pasting code and change a couple of words every time you need feature X. The KunstmaanGeneratorBundle gives you the possibility to generate code for new bundles, adminlists and can even make you a basic default website. That way you don't have to wait too long before you see some results and you have more time to do other things. Easy no?

## Installation

This bundle is compatible with all Symfony 2.1.* releases. More information about installing can be found in this line by line walkthrough of installing Symfony and all our bundles, please refer to the [Getting Started guide](http://bundles.kunstmaan.be/doc/01_GettingStarted.html) and enjoy the full blown experience.


## Use

Generate a Bundle :

```
app/console kuma:generate:bundle
```

Generate an Entity based on the [KunstmaanAdminBundle](https://github.com/Kunstmaan/KunstmaanAdminBundle)'s AbstractEntity

```
app/console kuma:generate:entity
```

```

Generate a [KunstmaanAdminList](https://github.com/Kunstmaan/KunstmaanAdminListBundle) for an Entity :

```
app/console kuma:generate:adminlist --entity=Bundle:Entity
```

### Website

Generate a default website using the Kunstmaan bundles :

```
app/console kuma:generate:default-site --namespace=Namespace\NamedBundle --prefix=tableprefix_

#### Additional Pages

Generate a search page based on the [KunstmaanNodeSearchBundle](https://github.com/Kunstmaan/KunstmaanNodeSearchBundle) :

```
app/console kuma:generate:searchpage --namespace=Namespace\NamedBundle --prefix=tableprefix_
```

## More

You can find more detailed information about these commands [here](https://github.com/Kunstmaan/KunstmaanGeneratorBundle/blob/master/Resources/doc/GeneratorBundle.md)

