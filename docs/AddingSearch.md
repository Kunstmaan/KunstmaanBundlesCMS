Another thing that almost every site needs is some sort of search functionality. And we've also written a bundle
that should make your life a bit easier when you want to implement it.

1) Prerequisites
----------------

To add search functionality you will have to install [ElasticSearch][1] first, since that's currently the only
search engine we support at this moment (we might add others as well, but haven't felt the need to yet, ElasticSearch
rocks!). For the PHP side of things we rely heavily on the [Sherlock][2] library.

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

2) Indexing nodes
-----------------

So, you've got ElasticSearch up and running, now you're ready to create the search page skeleton code, so just run :

    app/console kuma:generate:search

This will first ask for the bundle namespace (you can accept the default - Sandbox/WebsiteBundle), and finally it will
ask for the table name prefix, so enter sb_ as before.

The basic code skeleton should now be generated, so go ahead and create (and apply) a migration for the database
changes :

    app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate

This should make sure the necessary table (which will store the search pages) is created.

As before, we would like to be able to add the search page as a subpage of the homepage, so we need to add it to the
`getPossibleChildTypes` in the HomePage class, so open `src/Sandbox/WebsiteBundle/Entity/Pages/HomePage.php`, and add
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
                'class'=> 'Sandbox\WebsiteBundle\Entity\Pages\Search\SearchPage'
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

    curl -X GET http://localhost:9200/sandboxnodeindex/page/_search

This will list all pages that were indexed in the index that was created.

If you return to the search page, and enter `Styles` in the search box (or open `/app_dev.php/en/search?query=Styles&search=Search`
in your browser), you should see the Styles page (unless you already deleted it of course, just enter any page title
or some content).

For background information on our node search implementation refer to the [KunstmaanNodeSearchBundle documentation][6].

For background information on how to add your own custom indexes refer to the [KunstmaanSearchBundle documentation][7].



[1]:  http://www.elasticsearch.org/
[2]:  http://sherlockphp.com/
[3]:  http://www.elasticsearch.org/guide/
[4]:  http://www.elasticsearch.org/downloads/
[5]:  http://brew.sh/
[6]:  https://github.com/Kunstmaan/KunstmaanNodeSearchBundle/blob/master/Resources/doc/NodeSearchBundle.md
[7]:  https://github.com/Kunstmaan/KunstmaanSearchBundle/blob/master/Resources/doc/SearchBundle.md