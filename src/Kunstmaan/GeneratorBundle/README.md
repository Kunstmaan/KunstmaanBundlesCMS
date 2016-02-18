# KunstmaanGeneratorBundle 

[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanGeneratorBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanGeneratorBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/generator-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/generator-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/generator-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/generator-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanGeneratorBundle)](https://github.com/igrigorik/ga-beacon)

If you're like us, you like to build applications without having to do the same things over and over again and dislike copy/pasting code and change a couple of words every time you need feature X. The KunstmaanGeneratorBundle gives you the possibility to generate code for new bundles, adminlists and can even make you a basic default website. That way you don't have to wait too long before you see some results and you have more time to do other things. Easy no?

## Installation

This bundle is compatible with all Symfony 2.3.* releases. More information about installing can be found in this line by line walkthrough of installing Symfony and all our bundles, please refer to the [Getting Started guide](http://bundles.kunstmaan.be/getting-started) and enjoy the full blown experience.

## Use

Generate a Bundle :

```
bin/console kuma:generate:bundle
```

Generate an Entity based on the [KunstmaanAdminBundle](https://github.com/Kunstmaan/KunstmaanAdminBundle)'s AbstractEntity

```
bin/console kuma:generate:entity
```

Generate a [KunstmaanAdminList](https://github.com/Kunstmaan/KunstmaanAdminListBundle) for an Entity :

```
bin/console kuma:generate:adminlist --entity=Bundle:Entity
```

### Website

Generate a default website using the Kunstmaan bundles :

```
bin/console kuma:generate:default-site --namespace=Namespace\NamedBundle --prefix=tableprefix_
```

#### Search page

Generate a search page based on the [KunstmaanNodeSearchBundle](https://github.com/Kunstmaan/KunstmaanNodeSearchBundle) :

```
bin/console kuma:generate:searchpage --namespace=Namespace\NamedBundle --prefix=tableprefix_
```

#### Article : Overview and detail pages

Generate an overview page with article pages. The overview page contains a paginated list of its articles. The articles are managed by AdminLists.

```
bin/console kuma:generate:article --namespace=Namespace\NamedBundle --entity=Entity --prefix=tableprefix_
```

#### Page

Generate a new custom page :

```
bin/console kuma:generate:page --prefix=tableprefix_
```

#### PagePart

Generate a new page part page based on the [KunstmaanPagePartBundle](https://github.com/Kunstmaan/KunstmaanPagePartBundle) :

```
bin/console kuma:generate:pagepart --prefix=tableprefix_
```

## More

You can find more detailed information about these commands [here](https://github.com/Kunstmaan/KunstmaanGeneratorBundle/blob/master/Resources/doc/GeneratorBundle.md)

## Symfony 2.2

If you want to use this bundle for a Symfony 2.2 release, use the 2.2 branch.
