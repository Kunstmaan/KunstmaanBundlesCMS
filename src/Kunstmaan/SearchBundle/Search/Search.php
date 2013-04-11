<?php

namespace Kunstmaan\SearchBundle\Search;

use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;

/**
 * Search class which will delegate to the active SearchProvider
 */
class Search implements SearchProviderInterface
{
    /**
     * @var SearchProviderChain
     */
    private $searchProviderChain;

    /**
     * @var string
     */
    private $indexNamePrefix;

    /**
     * @var string
     */
    private $activeProvider;

    /**
     * @param SearchProviderChain $searchProviderChain
     * @param string              $indexNamePrefix
     * @param string              $activeProvider
     */
    public function __construct($searchProviderChain, $indexNamePrefix, $activeProvider)
    {
        $this->searchProviderChain = $searchProviderChain;
        $this->indexNamePrefix = $indexNamePrefix;
        $this->activeProvider = $activeProvider;
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
        return $this->getActiveProvider()->index($this->indexNamePrefix . $indexName);
    }

    public function document($indexName, $indexType, $doc, $uid)
    {
        return $this->getActiveProvider()->document($this->indexNamePrefix . $indexName, $indexType, $doc, $uid);
    }

    public function deleteDocument($indexName, $indexType, $uid)
    {
        return $this->getActiveProvider()->deleteDocument($this->indexNamePrefix . $indexName, $indexType, $uid);
    }

    public function delete($indexName)
    {
        return $this->getActiveProvider()->index($this->indexNamePrefix . $indexName)->delete();
    }

    public function search($indexName, $indexType, $querystring, $json = false)
    {
        return $this->getActiveProvider()->search($this->indexNamePrefix . $indexName, $indexType, $querystring, $json);
    }

    public function getName()
    {
        return "Search";
    }

    public function getIndexNamePrefix()
    {
        return $this->indexNamePrefix;
    }
}
