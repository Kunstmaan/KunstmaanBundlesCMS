<?php

namespace Kunstmaan\SearchBundle\Configuration;

/**
 * The chain of SearchConfigurations
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
     * @param                              $alias
     */
    public function addSearchConfiguration(SearchConfigurationInterface $searchConfiguration, $alias)
    {
        $this->searchConfigurations[$alias] = $searchConfiguration;
    }

    /**
     * Get a SearchConfiguration based on its alias
     *
     * @param $alias
     *
     * @return mixed
     */
    public function getSearchConfiguration($alias)
    {
        if (array_key_exists($alias, $this->searchConfiguration)) {
            return $this->searchConfigurations[$alias];
        } else {
            return;
        }
    }

    /**
     * Get all SearchConfigurations
     *
     * @return array
     */
    public function getSearchConfigurations()
    {
        return $this->searchConfigurations;
    }

}
