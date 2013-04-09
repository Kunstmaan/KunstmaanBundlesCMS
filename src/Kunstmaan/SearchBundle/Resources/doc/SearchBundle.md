# SearchBundle

## Adding a SearchConfiguration

If you want to index and search your own objects, you will need to create a SearchConfiguration.

## Adding a SearchProvider

Want to trade in Sherlock for another ElasticSearch library ? You can do that by creating a new SearchProvider.

## Searching

## Standard

By default the search method accepts a string which will be searched for in the 'title' and 'content' fields in the index.

## Advanced

If you want a more advanced query covering more fields, using range, boolean phrase queries, define new options for highlighting or add facets. You can use use JSON string as a parameter. In order to build that JSON string, you can use the Sherlock builders.