<?php

namespace Kunstmaan\NodeSearchBundle\PagerFanta\Adapter;

use Kunstmaan\NodeSearchBundle\Search\SearcherInterface;
use Elastica\ResultSet;

/**
 * Class SearcherRequestAdapter
 *
 * A Pagerfanta adapter to paginate Elastica search results.
 *
 * @package Kunstmaan\NodeSearchBundle\PagerFanta\Adapter
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
    private $facets;

    /**
     * @var array
     */
    private $hits;

    /**
     * @param SearcherInterface $searcher
     */
    public function __construct(SearcherInterface $searcher)
    {
        $this->searcher = $searcher;
        $this->facets   = array();
        $this->hits     = array();
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
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        return $this->response->getTotalHits();
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
        $this->hits = array();
        $this->facets = array();
        if (is_null($result)) {
            return null;
        }
        $this->collectHits($result);
        $this->collectFacets($result);
    }

    /**
     * @param ResultSet $result
     */
    protected function collectHits(ResultSet $result)
    {
        $data       = $result->getResults();
        foreach ($data as $item) {
            $content            = array();
            $content['_source'] = $item->getData();
            $highlights         = $item->getHighlights();
            if (!empty($highlights)) {
                $content['highlight'] = $highlights;
            }
            $this->hits[] = $content;
        }
    }

    /**
     * @param ResultSet $result
     *
     * @return bool
     */
    protected function collectFacets(ResultSet $result)
    {
        if (!$result->hasFacets()) {
            return false;
        }

        // Collect all facets
        $this->facets = $result->getFacets();

        return true;
    }
}
