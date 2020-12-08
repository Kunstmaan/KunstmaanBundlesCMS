<?php

namespace Kunstmaan\NodeSearchBundle\PagerFanta\Adapter;

use Elastica\ResultSet;
use Kunstmaan\NodeSearchBundle\Search\SearcherInterface;

/**
 * A Pagerfanta adapter to paginate Elastica search results.
 */
class SearcherRequestAdapter implements SearcherRequestAdapterInterface
{
    /**
     * @var SearcherInterface
     */
    private $searcher;

    /**
     * @var ResultSet
     */
    private $response;

    /**
     * @var ResultSet
     */
    private $suggests;

    /**
     * @var array
     */
    private $hits;

    /**
     * @var array
     */
    private $aggregations;

    public function __construct(SearcherInterface $searcher)
    {
        $this->searcher = $searcher;
        $this->hits = [];
        $this->aggregations = [];
    }

    /**
     * @return mixed
     */
    public function getSuggestions()
    {
        if (!isset($this->suggests)) {
            $this->suggests = $this->searcher->getSuggestions();
        }
        $suggests = $this->suggests->getSuggests();

        return $suggests['content-suggester'][0]['options'];
    }

    /**
     * Returns the number of results.
     *
     * @return int the number of results
     */
    public function getNbResults()
    {
        return $this->getResponse()->getTotalHits();
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @return array|\Traversable the slice
     */
    public function getSlice($offset, $length)
    {
        $this->response = $this->searcher->search($offset, $length);
        $this->processResponse($this->response);

        return $this->hits;
    }

    /**
     * @param ResultSet $result
     *
     * @return array|ResultSet
     */
    protected function processResponse(ResultSet $result = null)
    {
        $this->hits = [];
        if (\is_null($result)) {
            return null;
        }
        $this->collectHits($result);
        $this->collectAggregations($result);
    }

    protected function collectHits(ResultSet $result)
    {
        $data = $result->getResults();
        foreach ($data as $item) {
            $content = [];
            $content['_source'] = $item->getData();
            $highlights = $item->getHighlights();
            if (!empty($highlights)) {
                $content['highlight'] = $highlights;
            }
            $this->hits[] = $content;
        }
    }

    /**
     * @return bool
     */
    protected function collectAggregations(ResultSet $result)
    {
        if (!$result->hasAggregations()) {
            return false;
        }

        $this->aggregations = $result->getAggregations();

        return true;
    }

    /**
     * @return ResultSet
     */
    private function getResponse()
    {
        if (\is_null($this->response)) {
            $this->response = $this->searcher->search();
        }

        return $this->response;
    }

    /**
     * @return array
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }
}
