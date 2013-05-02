<?php

namespace Kunstmaan\NodeSearchBundle\PagerFanta\Adapter;

use Kunstmaan\SearchBundle\Search\Search;
use Pagerfanta\Adapter\AdapterInterface;

class SearchAdapter implements  AdapterInterface
{
    /**
     * @var Search
     */
    private $search;
    private $indexName;
    private $indexType;
    private $querystring;
    private $json;
    private $response;

    public function __construct($search, $indexName, $indexType, $querystring, $json = false)
    {
        $this->search = $search;
        $this->indexName = $indexName;
        $this->indexType = $indexType;
        $this->querystring = $querystring;
        $this->json = $json;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        $response = $this->search->search($this->indexName, $this->indexType, $this->querystring, $this->json);
        return $response['hits']['total'];
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        $this->response = $this->search->search($this->indexName, $this->indexType, $this->querystring, $this->json, $offset, $length);

        return $this->response['hits']['hits'];
    }
}
