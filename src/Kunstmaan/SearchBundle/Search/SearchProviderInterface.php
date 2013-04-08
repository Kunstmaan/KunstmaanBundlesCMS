<?php

namespace Kunstmaan\SearchBundle\Search;

interface SearchProviderInterface {

    /**
     * Returns a unique name for the SearchProvider
     *
     * @return string
     */
    public function getName();

    /**
     * Create a new index
     */
    public function index($indexName);

    /**
     * Add document to the index
     */
    public function document($indexName, $indexType, $doc);

    /**
     * Delete the index
     */
    public function delete($indexName);

}