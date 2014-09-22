<?php

namespace Kunstmaan\SearchBundle\Configuration;

/**
 * The chain of index configurations
 */
class SearchConfigurationChain
{
    /**
     * @var array
     */
    private $searchConfigurations;

    public function __construct()
    {
        $this->searchConfigurations = array();
    }

    /**
     * Add a SearchConfiguration to the chain
     *
     * @param SearchConfigurationInterface $searchConfiguration
     * @param string                       $alias
     */
    public function addConfiguration(SearchConfigurationInterface $searchConfiguration, $alias)
    {
        $this->searchConfigurations[$alias] = $searchConfiguration;
    }

    /**
     * Get an index configuration based on its alias
     *
     * @param string $alias
     *
     * @return SearchConfigurationInterface|null
     */
    public function getConfiguration($alias)
    {
        if (array_key_exists($alias, $this->searchConfiguration)) {
            return $this->searchConfigurations[$alias];
        }

        return null;
    }

    /**
     * Get all index configurations
     *
     * @return SearchConfigurationInterface[]
     */
    public function getConfigurations()
    {
        return $this->searchConfigurations;
    }
}
