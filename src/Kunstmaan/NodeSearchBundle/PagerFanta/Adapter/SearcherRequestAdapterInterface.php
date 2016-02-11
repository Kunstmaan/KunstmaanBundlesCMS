<?php

namespace Kunstmaan\NodeSearchBundle\PagerFanta\Adapter;

use Pagerfanta\Adapter\AdapterInterface;

interface SearcherRequestAdapterInterface extends AdapterInterface
{
    /**
     * @return mixed
     */
    public function getSuggestions();

    /**
     * @return array
     * @deprecated use getAggregations instead
     */
    public function getFacets();

    /**
     * @return array
     */
    public function getAggregations();
}
