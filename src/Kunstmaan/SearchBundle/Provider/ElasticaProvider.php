<?php

namespace Kunstmaan\SearchBundle\Provider;

use Elastica\Client;
use Elastica\Document;
use Elastica\Index;

class ElasticaProvider implements SearchProviderInterface
{
    /** @var Client $client The Elastica client */
    private $client;

    /** @var array $nodes An array of Elastica search nodes (each item in the array needs a host and port) */
    private $nodes = array();

    /**
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client instanceof Client) {
            $this->client = new Client(
                array( 'connections' =>
                    $this->nodes
                )
            );
        }

        return $this->client;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Elastica';
    }

    /**
     * @param string $indexName
     *
     * @return \Elastica\Index
     */
    public function createIndex($indexName)
    {
        return new Index($this->getClient(), $indexName);

    }

    /**
     * @param string $indexName
     *
     * @return \Elastica\Index
     */
    public function getIndex($indexName)
    {
        return $this->getClient()->getIndex($indexName);
    }

    /**
     * @param string $uid
     * @param array  $document
     * @param string $indexName
     * @param string $indexType
     *
     * @return \Elastica\Document
     */
    public function createDocument($uid, $document, $indexName = '', $indexType = '')
    {
        return new Document($uid, $document, $indexType, $indexName);
    }

    /**
     * @param string $indexName
     * @param string $indexType
     * @param array  $document
     * @param string $uid
     *
     * @return \Elastica\Response
     */
    public function addDocument($indexName, $indexType, $document, $uid)
    {
        $doc = $this->createDocument($uid, $document);

        return $this->getClient()->getIndex($indexName)->getType($indexType)->addDocument($doc);
    }

    /**
     * @param array  $docs
     * @param string $indexName
     * @param string $indexType
     *
     * @return \Elastica\Bulk\ResponseSet
     */
    public function addDocuments($docs, $indexName = '', $indexType = '')
    {
        // Ignore indexName & indexType for Elastica, they have already been set in the document...
        return $this->getClient()->addDocuments($docs);
    }

    /**
     * @param string $indexName
     * @param string $indexType
     * @param string $uid
     *
     * @return \Elastica\Bulk\ResponseSet
     */
    public function deleteDocument($indexName, $indexType, $uid)
    {
        $ids = array($uid);

        return $this->deleteDocuments($indexName, $indexType, $ids);
    }

    /**
     * @param string $indexName
     * @param string $indexType
     * @param array  $ids
     *
     * @return \Elastica\Bulk\ResponseSet
     */
    public function deleteDocuments($indexName, $indexType, array $ids)
    {
        $index = $this->getIndex($indexName);
        $type  = $index->getType($indexType);

        return $this->getClient()->deleteIds($ids, $index, $type);
    }

    /**
     * @param string $indexName
     *
     * @return \Elastica\Response
     */
    public function deleteIndex($indexName)
    {
        return $this->getIndex($indexName)->delete();
    }

    /**
     * @param string $host
     * @param int    $port
     */
    public function addNode($host, $port)
    {
        foreach ($this->nodes as $node) {
            if ($node['host'] === $host && $node['port'] === $port) {
                return;
            }
        }
        $this->nodes[] = array('host' => $host, 'port' => $port);
    }

    /**
     * @param array $nodes
     */
    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}
