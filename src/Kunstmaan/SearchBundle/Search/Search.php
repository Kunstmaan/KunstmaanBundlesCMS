<?php

namespace Kunstmaan\SearchBundle\Search;

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
        return $this->getActiveProvider()->document();
    }

    public function delete($indexName)
    {
        return $this->getActiveProvider()->index($indexName)->delete();
    }

    public function search($querystring, $type = array(), $tags = array())
    {
        return $this->getActiveProvider()->search($querystring, $type, $tags);
    }

    /**
     * Returns a unique name for the SearchProvider
     *
     * @return string
     */
    public function getName()
    {
        return "Search";
    }
}