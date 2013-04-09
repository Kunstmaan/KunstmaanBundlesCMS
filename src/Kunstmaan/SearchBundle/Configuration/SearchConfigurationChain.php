<?php

namespace Kunstmaan\SearchBundle\Configuration;

/**
 * The chain of SearchConfigurations
 */
class SearchConfigurationChain
{
    private $searchConfigurations;

    public function __construct()
    {
        $this->searchConfigurations = array();
    }

    public function addSearchConfiguration(SearchConfigurationInterface $searchConfiguration, $alias)
    {
        $this->searchConfigurations[$alias] = $searchConfiguration;
    }

    public function getSearchConfiguration($alias)
    {
        if (array_key_exists($alias, $this->searchConfiguration)) {
            return $this->searchConfigurations[$alias];
        } else {
            return;
        }
    }

    public function getSearchConfigurations()
    {
        return $this->searchConfigurations;
    }

}
