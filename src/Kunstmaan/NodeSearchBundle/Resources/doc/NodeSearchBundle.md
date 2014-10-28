# NodeSearchBundle

How it works!

## Indexing

All the top Nodes will be loaded from the NodeRepository, after wich it will recursively load all child Nodes. The configuration will then index each Node's NodeTranslations. From each NodeTranslation, the public NodeVersion will be loaded. Which means only online available pages will be indexed.

From each public NodeVersion, the following information will be indexed :

```PHP
$doc = array(
    "node_id" => $node->getId(),
    "nodetranslation_id" => $nodeTranslation->getId(),
    "nodeversion_id" => $publicNodeVersion->getId(),
    "title" => $nodeTranslation->getTitle(),
    "lang" => $nodeTranslation->getLang(),
    "slug" => $nodeTranslation->getFullSlug(),
    "type" => ClassLookup::getClassName($page),

);
```

### Type

By default the type will be the class name of the object. You can overridde this by implementing the SearchTypeInterface interface. This will allow you to bundle multiple classes together under the same type.

### Parent and Ancestors

In "parent" you will find the ID of the parent Node. In "ancestors" you will find a list of keys consisting of all the parent Nodes ID's (parent of the parent of the parent ...).

### Content

The field "content" will contain the bulk text content from the page. By default we check if the page implements the HasPagePartsInterface. If it does, it will iterate over all PageParts and load its template. The content of that template will be rendered and stripped from all tags.

### Extra fields

In case you want to index extra content or index addition information, create an EventListener on "kunstmaan_node_search.onIndexNode" which will allow you to manipulate the document before its being indexed.

### ID

The document for your page will be given a unique idea which will allow the document to be able to be updated.

## Updating

When a page is being updated, an event will be triggered to update the index with the latest information. If your page has been unpublished or deleted, it will also be deleted from the index.

## Searching

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

To override the template, simply create a view.html.twig in the 'app/Resources/KunstmaanNodeSearchBundle/views/SearchPage/' folder.

## More documentation

In case you want to extend your searching or even indexing, have a look at the documentation of the [KunstmaanSearchBundle](https://github.com/Kunstmaan/KunstmaanSearchBundle).