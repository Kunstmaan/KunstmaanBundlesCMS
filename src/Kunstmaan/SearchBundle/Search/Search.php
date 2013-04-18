<?php

namespace Kunstmaan\SearchBundle\Search;

use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;

/**
 * Search class which will delegate to the active SearchProvider
 * The active SearchProvider can be overridden by overriding the "kunstmaan_search.searchprovider" parameter
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

    /**
     * @inheritdoc
     */
    public function createIndex($indexName)
    {
        return $this->getActiveProvider()->createIndex($this->indexNamePrefix . $indexName);
    }

    /**
     * @inheritdoc
     */
    public function addDocument($indexName, $indexType, $doc, $uid)
    {
        return $this->getActiveProvider()->addDocument($this->indexNamePrefix . $indexName, $indexType, $doc, $uid);
    }

    /**
     * @inheritdoc
     */
    public function deleteDocument($indexName, $indexType, $uid)
    {
        return $this->getActiveProvider()->deleteDocument($this->indexNamePrefix . $indexName, $indexType, $uid);
    }

    /**
     * @inheritdoc
     */
    public function deleteIndex($indexName)
    {
        return $this->getActiveProvider()->deleteIndex($this->indexNamePrefix . $indexName);
    }

    /**
     * @inheritdoc
     */
    public function search($indexName, $indexType, $querystring, $json = false, $from = null, $size = null)
    {
        return $this->getActiveProvider()->search($this->indexNamePrefix . $indexName, $indexType, $querystring, $json, $from, $size);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return "Search";
    }

    /**
     * Get the prefix for the index' name
     *
     * @return string
     */
    public function getIndexNamePrefix()
    {
        return $this->indexNamePrefix;
    }
}
