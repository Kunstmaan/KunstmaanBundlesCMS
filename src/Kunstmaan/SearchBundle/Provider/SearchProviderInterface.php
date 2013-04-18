<?php

namespace Kunstmaan\SearchBundle\Provider;

/**
 * Interface for a SearchProvider
 */
interface SearchProviderInterface
{
    /**
     * Returns a unique name for the SearchProvider
     *
     * @return string
     */
    public function getName();

    /**
     * Create an index
     *
     * @param string $indexName Name of the index
     */
    public function createIndex($indexName);

    /**
     * Add a document to the index
     *
     * @param string $indexName Name of the index
     * @param string $indexType Type of the index to add the document to
     * @param array  $doc       The document to index
     * @param        $uid       Unique ID for this document, this will allow the document to be overwritten by new data instead of being duplicated
     */
    public function addDocument($indexName, $indexType, $doc, $uid);

    /**
     * delete a document from the index
     *
     * @param string $indexName Name of the index
     * @param string $indexType Type of the index the document is located
     * @param        $uid       Unique ID of the document to be delete
     */
    public function deleteDocument($indexName, $indexType, $uid);

    /**
     * Delete an index
     *
     * @param $indexName    Name of the index to delete
     */
    public function deleteIndex($indexName);

    /**
     * Search the index. The query string will by default search in the 'title' and 'content' fields.
     * When $json is set to true, the query string is assumed to be a JSON structure containing the entire query
     *
     * Returns an array containing the response from ElasticSearch, see : http://www.elasticsearch.org/guide/reference/api/search/request-body/
     *
     * @param string $indexName
     * @param string $indexType
     * @param string $querystring The query string
     * @param bool   $json        The $querystring is formatted as JSON, default set to false
     * @param null   $from        Offset from which the searchresults must start
     * @param null   $size        The number of hits to return
     *
     * @return array
     */
    public function search($indexName, $indexType, $querystring, $json = false, $from = null, $size = null);

}
