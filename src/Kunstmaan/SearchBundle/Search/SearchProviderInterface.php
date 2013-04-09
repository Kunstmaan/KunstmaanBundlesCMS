<?php

namespace Kunstmaan\SearchBundle\Search;

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
     * @param $indexName Name of the index
     */
    public function index($indexName);

    /**
     * Add a document to the index
     *
     * @param string    $indexName    Name of the index
     * @param string    $indexType    Type of the index to add the document to
     * @param           $doc          The document to index
     * @param           $uid          Unique ID for this document, this will allow the document to be overwritten by new data instead of being duplicated
     */
    public function document($indexName, $indexType, $doc, $uid);

    /**
     * Delete an index
     *
     * @param $indexName    Name of the index to delete
     */
    public function delete($indexName);

    /**
     * Search the index. The query string will by default search in the 'title' and 'content' fields.
     * When $json is set to true, the query string is assumed to be a JSON structure containing the entire query
     *
     * @param        $indexName
     * @param        $indexType
     * @param string $querystring  The query string
     * @param bool   $json         The $querystring is formatted as JSON, default set to false
     *
     * @return array
     */
    public function search($indexName, $indexType, $querystring, $json = false);

}
