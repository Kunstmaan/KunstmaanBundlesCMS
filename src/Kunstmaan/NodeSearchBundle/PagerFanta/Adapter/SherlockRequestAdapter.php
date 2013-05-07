<?php

namespace Kunstmaan\NodeSearchBundle\PagerFanta\Adapter;

use Kunstmaan\SearchBundle\Search\Search;
use Pagerfanta\Adapter\AdapterInterface;
use Sherlock\requests\SearchRequest;

class SherlockRequestAdapter implements  AdapterInterface
{
    /**
     * @var Search
     */
    private $search;
    private $indexName;
    private $indexType;

    /**
     * @var SearchRequest
     */
    private $request;
    private $response;
    private $fullResponse;

    public function __construct($search, $indexName, $indexType, $request)
    {
        $this->search = $search;
        $this->indexName = $indexName;
        $this->indexType = $indexType;
        $this->request = $request;
        $this->fullResponse = $this->search->search($this->indexName, $this->indexType, $this->request->toJSON(), true);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getFullResponse()
    {
        return $this->fullResponse;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        return $this->fullResponse['hits']['total'];
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
        $this->request->from($offset);
        $this->request->size($length);

        $this->response = $this->search->search($this->indexName, $this->indexType, $this->request->toJSON(), true);

        return $this->response['hits']['hits'];
    }
}
