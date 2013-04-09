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
     *
     * @return mixed
     */
    public function index($indexName);

    /**
     * Add a document to the index
     *
     * @param $indexName    Name of the index
     * @param $indexType    Type of the index to add the document to
     * @param $doc          The document to index
     * @param $uid          Unique ID for this document, this will allow the document to be overwritten by new data instead of being duplicated
     *
     * @return mixed
     */
    public function document($indexName, $indexType, $doc, $uid);

    /**
     * Delete an index
     *
     * @param $indexName    Name of the index to delete
     *
     * @return mixed
     */
    public function delete($indexName);

}
