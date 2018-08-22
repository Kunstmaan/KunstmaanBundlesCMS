<?php

namespace Kunstmaan\SearchBundle\Provider;

interface SearchProviderChainInterface 
{
    /**
     * Add a SearchProvider to the chain
     *
     * @param SearchProviderInterface $provider
     * @param string                  $alias
     */
    public function addProvider(SearchProviderInterface $provider, $alias);

    /**
     * Get a SearchProvider based on its alias
     *
     * @param string $alias
     *
     * @return SearchProviderInterface|null
     */
    public function getProvider($alias);

    /**
     * Get all SearchProviders
     *
     * @return SearchProviderInterface[]
     */
    public function getProviders();
}
