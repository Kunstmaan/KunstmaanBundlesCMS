<?php

namespace Kunstmaan\SearchBundle\Search;

class Search implements SearchProviderInterface {

    /**
     * @var SearchProviderInterface
     */
    private $providers;

    /**
     * @var string
     */
    private $activeProvider = "Sherlock";

    public function __construct()
    {
        $searchProviderChain = $this->getContainer()->get('kunstmaan_search.searchprovider_chain');
        $this->providers = $searchProviderChain->getSearchProviders();
    }

    public function getActiveProvider()
    {
        return $this->providers[$this->activeProvider];
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