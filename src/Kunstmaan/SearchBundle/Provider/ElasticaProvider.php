<?php

namespace Kunstmaan\SearchBundle\Provider;

use Elastica\Client;
use Elastica\Document;
use Elastica\Index;

class ElasticaProvider implements SearchProviderInterface
{
    /** @var Client The Elastica client */
    private $client;

    /** @var array An array of Elastica search nodes (each item in the array needs a host and port) */
    private $nodes = [];

    /**
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client instanceof Client) {
            $this->client = new Client(
                ['connections' => $this->nodes,
                ]
            );

            //NEXT_MAJOR: remove checks and update ruflin/elastica dependency constraints
            if (!class_exists(\Elastica\Mapping::class)) {
                @trigger_error('Using a version of ruflin/elastica below v7.0 is deprecated since KunstmaanSearchBundle 5.6 and support for older versions will be removed in KunstmaanSearchBundle 6.0. Upgrade to ruflin/elastica and elasticsearch v7 instead.', E_USER_DEPRECATED);
            }
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
        if (method_exists(Document::class, 'setType')) {
            // @phpstan-ignore-next-line
            return new Document($uid, $document, $indexType, $indexName);
        }

        return new Document($uid, $document, $indexName);
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
        $index = $this->getClient()->getIndex($indexName);
        if (method_exists($index, 'getType')) {
            return $index->getType($indexType)->addDocument($doc);
        }

        return $index->addDocument($doc);
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
        $ids = [$uid];

        return $this->deleteDocuments($indexName, $indexType, $ids);
    }

    /**
     * @param string $indexName
     * @param string $indexType
     *
     * @return \Elastica\Bulk\ResponseSet
     */
    public function deleteDocuments($indexName, $indexType, array $ids)
    {
        $index = $this->getIndex($indexName);

        if (method_exists($index, 'getType')) {
            $type = $index->getType($indexType);

            return $this->getClient()->deleteIds($ids, $index, $type);
        }

        return $this->getClient()->deleteIds($ids, $index);
    }

    /**
     * @param string $indexName
     *
     * @return \Elastica\Response|null
     */
    public function deleteIndex($indexName)
    {
        $index = $this->getIndex($indexName);
        if ($index->exists()) {
            return $index->delete();
        }

        return null;
    }

    /**
     * @param string      $host
     * @param int         $port
     * @param string|null $username
     * @param string|null $password
     */
    public function addNode($host, $port, $username = null, $password = null)
    {
        foreach ($this->nodes as $node) {
            if ($node['host'] === $host && $node['port'] === $port) {
                return;
            }
        }

        $authHeader = null;
        if (null !== $username && $password !== null) {
            $authHeader = ['Authorization' => 'Basic ' . base64_encode($username . ':' . $password)];
        }

        $this->nodes[] = ['host' => $host, 'port' => $port, 'headers' => $authHeader];
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
