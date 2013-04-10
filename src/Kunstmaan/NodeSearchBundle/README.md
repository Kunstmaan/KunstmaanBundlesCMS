# About the KunstmaanNodeSearchBundle

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

Add the SearchPage as a possible child to a page in your website :
```PHP
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array(
            array(
                'name' => 'Search',
                'class'=> "Kunstmaan\NodeSearchBundle\Entity\SearchPage"
            )
        );
    }
```

## Documentation

Find more documentation on how it works [here](https://github.com/Kunstmaan/KunstmaanNodeSearchBundle/tree/master/Resources/doc/NodeSearchBundle.md)