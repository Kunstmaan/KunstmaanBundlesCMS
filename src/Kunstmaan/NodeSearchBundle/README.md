# KunstmaanNodeSearchBundle

[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanNodeSearchBundle.png)](https://travis-ci.org/Kunstmaan/KunstmaanNodeSearchBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/node-search-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/node-search-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/node-search-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/node-search-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanNodeSearchBundle)](https://github.com/igrigorik/ga-beacon)

This bundle uses the [KunstmaanSearchBundle](https://github.com/Kunstmaan/KunstmaanSearchBundle) to search through Nodes from the [KunstmaanNodeBundle](https://github.com/Kunstmaan/KunstmaanNodeBundle)

## Installation

composer.json
```json
    "require": {
        "kunstmaan/node-search-bundle": "*"
    },
```

AppKernel.php:
```PHP
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Kunstmaan\NodeSearchBundle\KunstmaanNodeSearchBundle(),
            // ...
        );
```

## Configuration

### SearchPage

Extend the AbstractSearchPage and add your new class as a possible child to a page in your website :
```PHP
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array(
            array(
                'name' => 'Search page',
                'class'=> "Acme\DemoBundle\Entity\SearchPage"
            )
        );
    }
```

## Documentation

Find more documentation on how it works [here](https://github.com/Kunstmaan/KunstmaanNodeSearchBundle/tree/master/Resources/doc/NodeSearchBundle.md)
