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
     * @param SearchProviderChainInterface $searchProviderChain
     * @param string                       $indexNamePrefix
     * @param string                       $activeProvider
     */
    public function __construct(SearchProviderChainInterface $searchProviderChain, $indexNamePrefix, $activeProvider)
    {
        $this->searchProviderChain = $searchProviderChain;
        $this->indexNamePrefix     = $indexNamePrefix;
        $this->activeProvider      = $activeProvider;
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
    public function getIndex($indexName)
    {
        return $this->getActiveProvider()->getIndex($this->indexNamePrefix . $indexName);
    }

    /**
     * @inheritdoc
     */
    public function getClient()
    {
        return $this->getActiveProvider()->getClient();
    }

    /**
     * @inheritdoc
     */
    public function createDocument($uid, $document, $indexName = '', $indexType = '')
    {
        return $this->getActiveProvider()->createDocument(
            $uid,
            $document,
            $this->indexNamePrefix . $indexName,
            $indexType
        );
    }

    /**
     * @inheritdoc
     */
    public function addDocument($uid, $document, $indexType, $indexName)
    {
        return $this->getActiveProvider()->addDocument(
            $this->indexNamePrefix . $indexName,
            $indexType,
            $document,
            $uid
        );
    }

    /**
     * @inheritdoc
     */
    public function addDocuments($documents, $indexName = '', $indexType = '')
    {
        return $this->getActiveProvider()->addDocuments($documents, $indexName, $indexType);
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
    public function deleteDocuments($indexName, $indexType, array $ids)
    {
        return $this->getActiveProvider()->deleteDocuments($this->indexNamePrefix . $indexName, $indexType, $ids);
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
