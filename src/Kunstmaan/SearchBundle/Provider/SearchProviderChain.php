<?php

namespace Kunstmaan\SearchBundle\Provider;

/**
 * The chain of SearchProviders
 */
class SearchProviderChain implements SearchProviderChainInterface
{
    /** @var SearchProviderInterface[] */
    private $providers;

    public function __construct()
    {
        $this->providers = array();
    }

    /**
     * Add a SearchProvider to the chain
     *
     * @param SearchProviderInterface $provider
     * @param string                  $alias
     */
    public function addProvider(SearchProviderInterface $provider, $alias)
    {
        $this->providers[$alias] = $provider;
    }

    /**
     * Get a SearchProvider based on its alias
     *
     * @param string $alias
     *
     * @return SearchProviderInterface|null
     */
    public function getProvider($alias)
    {
        if (array_key_exists($alias, $this->providers)) {
            return $this->providers[$alias];
        }

        return null;
    }

    /**
     * Get all SearchProviders
     *
     * @return SearchProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
