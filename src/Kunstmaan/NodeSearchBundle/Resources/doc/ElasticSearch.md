# Elastic search support

We have added support for Elastic search up to version 6.x.

## Dynamic configuration
For the mapping and configuration we detect which version of the library your project is using.
Dependencies for using the configuration of version 6 is that your project uses PHP 7 and ruflin/elastica >= 6.0. 

## Difference between versions

The biggest change in version 6, is that Elastica has dropped support for the "_all" meta field.
(see https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-all-field.html)

When using version 6.x, then you will no longer be able to use the "include_in_all" and "index => 'not_analysed".
use 'index => false' instead. Also some of the index types have been changed and some fields are no longer indexed (like the 'created' and 'updated' fields).

### Example

Some examples of the mapping that have changed (for the kunstmaan_node_search extension):

```
'node_id' => [
    'type' => 'integer',
    'include_in_all' => false,
    'index' => 'not_analyzed'
],
...
'view_roles' => [
    'type' => 'string',
    'include_in_all' => true,
    'index' => 'not_analyzed',
],
```

in 


```
'node_id' => [
    'type' => 'integer',
],
...
'view_roles' => [
    'type' => 'keyword',
],
```

### Link to elastic search documentation

https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html