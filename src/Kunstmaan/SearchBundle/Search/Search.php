<?php

namespace Kunstmaan\SearchBundle\Search;

/**
 * Search class which will delegate to the active SearchProvider
 */
class Search implements SearchProviderInterface {

    private $searchProviderChain;

    /**
     * @var string
     */
    private $activeProvider = "Sherlock";

    /**
     * @param SearchProviderChain $searchProviderChain
     */
    public function __construct($searchProviderChain)
    {
        $this->searchProviderChain = $searchProviderChain;
    }

    /**
     * Get the current active SearchProvider
     *
     * @return SearchProviderInterface
     */
    public function getActiveProvider()
    {
        return $this->searchProviderChain->getSearchProvider($this->activeProvider);
    }

    public function index($indexName)
    {
        return $this->getActiveProvider()->index($indexName);
    }

    public function document($indexName, $indexType, $doc)
    {
        return $this->getActiveProvider()->document($indexName, $indexType, $doc);
    }

    public function delete($indexName)
    {
        return $this->getActiveProvider()->index($indexName)->delete();
    }

    public function search($querystring, $type = array(), $tags = array())
    {
        return $this->getActiveProvider()->search($querystring, $type, $tags);
    }

    public function getName()
    {
        return "Search";
    }
}