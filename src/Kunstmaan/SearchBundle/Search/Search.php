<?php

namespace Kunstmaan\SearchBundle\Search;

use Kunstmaan\SearchBundle\Provider\SearchProviderChainInterface;
use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;

/**
 * Search class which will delegate to the active SearchProvider
 * The active SearchProvider can be overridden by overriding the "kunstmaan_search.search" parameter
 */
class Search implements SearchProviderInterface
{
    /**
     * @var SearchProviderChainInterface
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
     * @param string $indexNamePrefix
     * @param string $activeProvider
     */
    public function __construct(SearchProviderChainInterface $searchProviderChain, $indexNamePrefix, $activeProvider)
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
        return $this->searchProviderChain->getProvider($this->activeProvider);
    }

    public function createIndex($indexName)
    {
        return $this->getActiveProvider()->createIndex($this->indexNamePrefix . $indexName);
    }

    public function getIndex($indexName)
    {
        return $this->getActiveProvider()->getIndex($this->indexNamePrefix . $indexName);
    }

    public function getClient()
    {
        return $this->getActiveProvider()->getClient();
    }

    public function createDocument($uid, $document, $indexName = '', $indexType = '')
    {
        $indexName = strtolower($indexName);

        return $this->getActiveProvider()->createDocument(
            $uid,
            $document,
            $this->indexNamePrefix . $indexName,
            $indexType
        );
    }

    public function addDocument($uid, $document, $indexType, $indexName)
    {
        $indexName = strtolower($indexName);

        return $this->getActiveProvider()->addDocument(
            $this->indexNamePrefix . $indexName,
            $indexType,
            $document,
            $uid
        );
    }

    public function addDocuments($documents, $indexName = '', $indexType = '')
    {
        $indexName = strtolower($indexName);

        return $this->getActiveProvider()->addDocuments($documents, $indexName, $indexType);
    }

    public function deleteDocument($indexName, $indexType, $uid)
    {
        $indexName = strtolower($indexName);

        return $this->getActiveProvider()->deleteDocument($this->indexNamePrefix . $indexName, $indexType, $uid);
    }

    public function deleteDocuments($indexName, $indexType, array $ids)
    {
        $indexName = strtolower($indexName);

        return $this->getActiveProvider()->deleteDocuments($this->indexNamePrefix . $indexName, $indexType, $ids);
    }

    public function deleteIndex($indexName)
    {
        $indexName = strtolower($indexName);

        return $this->getActiveProvider()->deleteIndex($this->indexNamePrefix . $indexName);
    }

    public function getName()
    {
        return 'Search';
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
