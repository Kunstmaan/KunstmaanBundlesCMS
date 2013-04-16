# SearchBundle

## Searching

### Standard

By default the search method accepts a string which will be searched for in the 'title' and 'content' fields in the index.

```PHP
$search = $container->get('kunstmaan_search.search');
$response = $search->search("testindex", "testtype", $querystring);
```

### Advanced

It's possible to use a more advanced query covering more fields, using range, boolean phrase queries, define new options for highlighting or add facets. Use a JSON string as a parameter. In order to build that JSON string, [Sherlock](https://github.com/polyfractal/sherlock) has some handy builders.

The $json parameter is expected to contain the full query in JSON format.

```PHP
$search = $container->get('kunstmaan_search.search');
$response = $search->search("testindex", "testtype", $json, true);
```

#### Query

One way to build your query is by using the Sherlock QueryBuilder :

```PHP
$titleQuery = Sherlock::queryBuilder()->Wildcard()->field("title")->value($querystring);
$contentQuery = Sherlock::queryBuilder()->Wildcard()->field("content")->value($querystring);

$query = Sherlock::queryBuilder()->Bool()->should($titleQuery, $contentQuery)->minimum_number_should_match(1);
```

See [Sherlock](https://github.com/polyfractal/sherlock) for more information regarding query building.

#### Filters

See [Sherlock](https://github.com/polyfractal/sherlock) for more information regarding filters.

#### Facets

Add facets to your search, Sherlock supplies a FacetBuilder to aid you :

```PHP
$tagFacet = Sherlock::facetBuilder()->Terms()->fields("tags")->facetname("tag");
$request->facets($tagFacet);
```

See [Sherlock](https://github.com/polyfractal/sherlock) for more information regarding facets.

#### Highlighting

Sherlock also supplies a HighlightBuilder to add highlighting to your search results.

```PHP
$highlight = Sherlock::highlightBuilder()->Highlight()->pre_tags(array("<strong>"))->post_tags(array("</strong>"))->fields(array("content" => array("fragment_size" => 150, "number_of_fragments" => 1)));
$request->highlight($highlight);
```

See [Sherlock](https://github.com/polyfractal/sherlock) for more information regarding highlighting.

## Adding a search configuration

If you want to index and search your own objects, you will need to create a SearchConfiguration.

Create a new class and implement the [SearchConfigurationInterface](https://github.com/Kunstmaan/KunstmaanSearchBundle/blob/sherlock/Configuration/SearchConfigurationInterface.php).
Implement the three methods from the interface.

### Implement methods

#### create

This method is expected to create one or more indexes. Sherlock has a MappingBuilder to help create mappings for your index.

```PHP
    public function create()
    {
        $index = $this->search->index($this->indexName);

        $index->mappings(
            Sherlock::mappingBuilder('type')->String()->field('title'),
            Sherlock::mappingBuilder('type')->String()->field('content'),
            Sherlock::mappingBuilder('type')->String()->field('tags')->analyzer('keyword'),
        );

        $index->create();
    }
```
See [Sherlock](https://github.com/polyfractal/sherlock) for more information regarding the MappingBuilder.

#### index

The index method will be called upon to populate your index with documents. With '$indexName' and '$indexType' parameter you can control where this document is being put.

```PHP
    public function index()
    {
        $doc = array(
            "title" => "Test Title",
            "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec lacus tortor, ut ultricies libero. Donec dapibus erat a nisi condimentum viverra."
        );
        $uid = "a_unique_doc_id";
        $this->search->document($indexName, $indexType, $doc, $uid);
    }
```

#### delete

Delete the index(es) in this method.

```PHP
    public function delete()
    {
        $this->search->delete($indexName);
    }
```
### Tagged service

Add the SearchConfiguration class as a tagged service to the services.yml.

Here's an example from the NodeSearchConfiguration, used to index nodes from the [KunstmaanNodeBundle](https://github.com/Kunstmaan/KunstmaanNodeBundle)

<pre>
parameters:
    kunstmaan_search.searchconfiguration.node.class: Kunstmaan\SearchBundle\Node\NodeSearchConfiguration
services:
    kunstmaan_search.searchconfiguration.node:
        class: "%kunstmaan_search.searchconfiguration.node.class%"
        arguments: ["@service_container", "@kunstmaan_search.search"]
        tags:
            - { name: kunstmaan_search.searchconfiguration, alias: Node }
</pre>

Using the tag "kunstmaan_search.searchconfiguration", the SearchConfiguration will be added to the SearchConfigurationChain and in turn be called upon when creating, deleting and populating the indexes.

## Adding a search provider

Want to trade in Sherlock for another ElasticSearch library ? It can be done by creating a new SearchProvider.

Create a new class and implement the [SearchProviderInterface](https://github.com/Kunstmaan/KunstmaanSearchBundle/blob/sherlock/Search/SearchProviderInterface.php).

### Implement methods

#### index

Create the index

#### document

Add the document to the index

#### delete

Delete the index

#### search

The search method allows 2 ways of searching. A standard search which is expected to search the 'title' and 'content' field for the $querystring. When the $json parameter is set to true, the $querystring will contain the full JSON request for ElasticSearch.

## Commands

Create indexes by performing the following command. It will iterate over all SearchConfigurations and call the create() method.
```
kuma:search:setup
```
Use the following command to populate the indexes. Use the 'full' argument to delete and create the indexes again. This command will iterate over all SearchConfigurations and call the index() method.
```
kuma:search:populate
```
```
kuma:search:populate full
```
Next command is used to delete the indexes. It will iterate over all SearchConfigurations and call the delete() method.
```
kuma:search:delete
```
