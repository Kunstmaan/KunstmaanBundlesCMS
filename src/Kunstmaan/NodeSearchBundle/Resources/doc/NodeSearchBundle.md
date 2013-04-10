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

### Parent and Ancestors

In "parent" you will find the ID of the parent Node. In "ancestors" you will find a list of keys consisting of all the parent Nodes ID's (parent of the parent of the parent ...).

### Content

The field "content" will contain the bulk text content from the page. By default we check if the page implements the HasPagePartsInterface. If it does, it will iterate over all PageParts and load its template. The content of that template will be rendered and stripped from all tags. In case you have content in additional fields, you will need to implement the HasCustomContent interface. This interface contains the getCustomSearchContent() method which is expected to return a string containing additional content that will be added to the rest of the content to be indexed.

### Taggable

In case your page implements the DoctrineExtensions\Taggable\Taggable interface, the tags will also be indexed in the field "tags".

### ID

The document for your page will be given a unique idea which will allow the document to be able to be updated.

## Updating

When a page is being updated, an event will be triggered to update the index with the latest information. If your page has been unpublished or deleted, it will also be deleted from the index.

## Searching

You can add the SearchPage to your website by adding it as a possible child to any page in your website :

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

In case you want to extend your searching or even indexing, have a look at the documentation of the [KunstmaanSearchBundle](https://github.com/Kunstmaan/KunstmaanSearchBundle).