# Adding a search engine

Another thing that almost every site needs is some sort of search functionality. By default we install this search engine in via the default site generator, but it's good to know what's happening there:

## Prerequisites

To add search functionality you will have to install [ElasticSearch][1] first, since that's currently the only
search engine we support at this moment (we might add others as well, but haven't felt the need to yet, ElasticSearch
rocks!). For the PHP side of things we rely heavily on the [Elastica][2] library.

Installing and configuring ElasticSearch is a bit out of scope here, so just have a look at the excellent
[ElasticSearch Guide][3] and install it.

For Linux users there are DEB and RPM packages available on the [ElasticSearch downloads][4] page, so installing it
can be as easy as :

```
wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-0.90.3.deb
sudo dpkg -i elasticsearch-0.90.3.deb
sudo service elasticsearch start
```

At least, if you're running Debian or Ubuntu :p.

For OS X users we suggest installing it using [Homebrew][5], so you can simply run :

```
brew install elasticsearch
launchctl load ~/Library/LaunchAgents/homebrew.mxcl.elasticsearch.plist
```

And you should be good to go!

To test if it actually is running you can run :

    curl -X GET http://localhost:9200/

Which should return something like this :

```json
{
  "ok" : true,
  "status" : 200,
  "name" : "Smythe, Spencer",
  "version" : {
    "number" : "0.90.3",
    "build_hash" : "5c38d6076448b899d758f29443329571e2522410",
    "build_timestamp" : "2013-08-06T13:18:31Z",
    "build_snapshot" : false,
    "lucene_version" : "4.4"
  },
  "tagline" : "You Know, for Search"
}
```

## Indexing nodes

So, you've got ElasticSearch up and running, now you're ready to create the search page skeleton code, so just run :

    app/console kuma:generate:search

This will first ask for the bundle namespace (you can accept the default - MyProject/WebsiteBundle), and finally it will
ask for the table name prefix, so enter myproject_websitebundle_ as before.

The basic code skeleton should now be generated, so go ahead and create (and apply) a migration for the database
changes :

    app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate

This should make sure the necessary table (which will store the search pages) is created.

As before, we would like to be able to add the search page as a subpage of the homepage, so we need to add it to the
`getPossibleChildTypes` in the HomePage class, so open `src/MyProject/WebsiteBundle/Entity/Pages/HomePage.php`, and add
it :

```php
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
	return array(
	    ...
	    ),
	    array(
		'name' => 'Search Page',
		'class'=> 'MyProject\WebsiteBundle\Entity\Pages\Search\SearchPage'
	    )
	);
    }
```

After adding this snippet, you should be able to add a search page on the homepage in the backend, so go ahead
and do that (use `Search` as title) - and make sure you publish it after it is created.

If you now go to `/app_dev.php/en/search` in your browser you should see the search page, but currently it's not yet
indexing anything, so let's set up the index first, go ahead and run :

```
app/console kuma:search:setup
app/console kuma:search:populate full
```

This should create the search index (if it does not exist yet) and populate it as well.

If you would like to take a quick look at what is indexed, you could run the following :

    curl -X GET http://localhost:9200/myprojectnodeindex/page/_search

This will list all pages that were indexed in the index that was created.

If you return to the search page, and enter `Styles` in the search box (or open `/app_dev.php/en/search?query=Styles&search=Search`
in your browser), you should see the Styles page (unless you already deleted it of course, just enter any page title
or some content).

## How it works

### Indexing

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

#### Type

By default the type will be the class name of the object. You can overridde this by implementing the SearchTypeInterface interface. This will allow you to bundle multiple classes together under the same type.

#### Parent and Ancestors

In "parent" you will find the ID of the parent Node. In "ancestors" you will find a list of keys consisting of all the parent Nodes ID's (parent of the parent of the parent ...).

#### Content

The field "content" will contain the bulk text content from the page. By default we check if the page implements the HasPagePartsInterface. If it does, it will iterate over all PageParts and load its template. The content of that template will be rendered and stripped from all tags.

#### Extra fields

In case you want to index extra content or index addition information, create an EventListener on "kunstmaan_node_search.onIndexNode" which will allow you to manipulate the document before its being indexed.

#### ID

The document for your page will be given a unique idea which will allow the document to be able to be updated.

### Updating

When a page is being updated, an event will be triggered to update the index with the latest information. If your page has been unpublished or deleted, it will also be deleted from the index.

### Searching

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

## Search configurations

### Adding a search configuration

If you want to index and search your own objects, you will need to create a SearchConfiguration.

Create a new class and implement the [SearchConfigurationInterface](https://github.com/Kunstmaan/KunstmaanSearchBundle/blob/master/Configuration/SearchConfigurationInterface.php).
Implement the three methods from the interface.

#### Implement methods

##### createIndex

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

##### populateIndex

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

##### deleteIndex

Delete the index(es) in this method.

```PHP
    public function deleteIndex()
    {
	$this->searchProvider->deleteIndex($indexName);
    }
```

#### Tagged service

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

### Adding a custom search provider

Want to trade in Elastica for another ElasticSearch library? It can be done by creating a new SearchProvider.

Create a new class and implement the [SearchProviderInterface](https://github.com/Kunstmaan/KunstmaanSearchBundle/).

#### Implement methods

##### createIndex

Create the index

##### addDocument

Add the document to the index

##### deleteDocument

Delete the document from the index

##### deleteIndex

Delete the index

##### search

The search method allows 2 ways of searching. A standard search which is expected to search the 'title' and 'content' field for the $querystring. When the $json parameter is set to true, the $querystring will contain the full JSON request for ElasticSearch.

### Commands

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

[1]:  http://www.elasticsearch.org/
[2]:  https://github.com/ruflin/Elastica
[3]:  http://www.elasticsearch.org/guide/
[4]:  http://www.elasticsearch.org/downloads/
[5]:  http://brew.sh/
