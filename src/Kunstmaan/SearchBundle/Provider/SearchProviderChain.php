<?php

namespace Kunstmaan\SearchBundle\Provider;

/**
 * The chain of SearchProviders
 */
class SearchProviderChain
{
    private $searchProviders;

    public function __construct()
    {
        $this->searchProviders = array();
    }

    /**
     * Add a SearchProvider to the chain
     *
     * @param SearchProviderInterface $searchProvider
     * @param                         $alias
     */
    public function addSearchProvider(SearchProviderInterface $searchProvider, $alias)
    {
        $this->searchProviders[$alias] = $searchProvider;
    }

    /**
     * Get a SearchProvider based on its alias
     *
     * @param $alias
     *
     * @return mixed
     */
    public function getSearchProvider($alias)
    {
        if (array_key_exists($alias, $this->searchProviders)) {
            return $this->searchProviders[$alias];
        } else {
            return;
        }
    }

    /**
     * Get all SearchProviders
     *
     * @return array
     */
    public function getSearchProviders()
    {
        return $this->searchProviders;
    }
}
