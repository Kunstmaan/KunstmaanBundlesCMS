<?php

namespace Kunstmaan\SearchBundle\Search;

class SearchProviderChain {
    
    private $searchProviders;

    public function __construct()
    {
        $this->searchProviders = array();
    }

    public function addSearchProvider(SearchProviderInterface $searchProvider, $alias)
    {
        $this->searchProviders[$alias] = $searchProvider;
    }

    public function getSearchProvider($alias)
    {
        if (array_key_exists($alias, $this->searchProviders)) {
            return $this->searchProviders[$alias];
        }
        else {
            return;
        }
    }

    public function getSearchProviders()
    {
        return $this->searchProviders;
    }
}