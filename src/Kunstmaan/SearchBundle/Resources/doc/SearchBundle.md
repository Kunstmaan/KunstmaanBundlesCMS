# SearchBundle

## Searching

In case you are using the [KunstmaanNodeBundle](https://github.com/Kunstmaan/KunstmaanNodeBundle), you can use the [KunstmaanNodeSearchBundle](https://github.com/Kunstmaan/KunstmaanNodeSearchBundle) which offers you an implementation of this SearchBundle to search your pages. It contains a basic SearchPage to start with.


## Adding a search configuration

If you want to index and search your own objects, you will need to create a SearchConfiguration.

Create a new class and implement the [SearchConfigurationInterface](https://github.com/Kunstmaan/KunstmaanSearchBundle/blob/master/Configuration/SearchConfigurationInterface.php).
Implement the three methods from the interface.

### Implement methods

#### createIndex

This method is expected to create one or more indexes. Elastica has a Mapping class to help create mappings for your index.

```PHP
    public function createIndex()
    {
	// build new index
	$index = $this->searchProvider->createIndex($this->indexName);

	// create mapping
	foreach ($this->locales as $locale) {
	    $this->setMapping($index, $locale);
	}
    }

    /**
     * @param \Elastica\Index $index
     * @param string          $lang
     */
    private function setMapping(\Elastica\Index $index, $lang = 'en')
    {
	$mapping = $this->getMapping($index, $lang);
	$mapping->send();
	$index->refresh();
    }
```

Refer to the [Elastica documentation](http://elastica.io/) for more information regarding the Mapping.

#### populateIndex

The index method will be called upon to populate your index with documents. With the '$indexName' and '$indexType' parameter you can control where this document will be stored.

```PHP
    public function populateIndex()
    {
        $doc = array(
            "title" => "Test Title",
            "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec lacus tortor, ut ultricies libero. Donec dapibus erat a nisi condimentum viverra."
        );
        $uid = "a_unique_doc_id";
	$this->searchProvider->createDocument($doc, $uid, $indexName, $indexType);
    }
```

When processing the objects, please be aware there's an interface "IndexableInterface" which provides the "isIndexable()" method. This method should return false when the object in question should not be indexed.

#### deleteIndex

Delete the index(es) in this method.

```PHP
    public function deleteIndex()
    {
	$this->searchProvider->deleteIndex($indexName);
    }
```

### Tagged service

Add the SearchConfiguration class as a tagged service to services.yml.

Here's an example from the NodeSearchConfiguration, used to index nodes from the [KunstmaanNodeBundle](https://github.com/Kunstmaan/KunstmaanNodeBundle)

<pre>
parameters:
    kunstmaan_node_search.search_configuration.node.class: Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration
    kunstmaan_node_search.indexname: "nodeindex"
    kunstmaan_node_search.indextype: "page"
    kunstmaan_node_search.node_index_update.listener.class: Kunstmaan\NodeSearchBundle\EventListener\NodeIndexUpdateEventListener

services:
    kunstmaan_node_search.search_configuration.node:
	class: %kunstmaan_node_search.search_configuration.node.class%
	arguments: ["@service_container", "@kunstmaan_search.search", "%kunstmaan_node_search.indexname%", "%kunstmaan_node_search.indextype%"]
	calls:
	    - [ setAclProvider, ["@security.acl.provider"]]
        tags:
	    - { name: kunstmaan_search.search_configuration, alias: Node }
</pre>

Using the tag "kunstmaan_search.search_configuration", the SearchConfiguration will be added to the SearchConfigurationChain and in turn be called upon when creating, deleting and populating the indexes.

## Adding a custom search provider

Want to trade in Elastica for another ElasticSearch library? It can be done by creating a new SearchProvider.

Create a new class and implement the [SearchProviderInterface](https://github.com/Kunstmaan/KunstmaanSearchBundle/).

### Implement methods

#### createIndex

Create the index

#### addDocument

Add the document to the index

#### deleteDocument

Delete the document from the index

#### deleteIndex

Delete the index

#### search

The search method allows 2 ways of searching. A standard search which is expected to search the 'title' and 'content' field for the $querystring. When the $json parameter is set to true, the $querystring will contain the full JSON request for ElasticSearch.

## Commands

Create indexes by performing the following command. It will iterate over all SearchConfigurations and call the createIndex() method.
```
kuma:search:setup
```
Use the following command to populate the indexes. Use the 'full' argument to delete and create the indexes again. This command will iterate over all SearchConfigurations and call the populateIndex() method.
```
kuma:search:populate
```
```
kuma:search:populate full
```
Next command is used to delete the indexes. It will iterate over all SearchConfigurations and call the deleteIndex() method.
```
kuma:search:delete
```
