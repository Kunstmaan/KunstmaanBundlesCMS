<?php

namespace Kunstmaan\NodeSearchBundle\PagerFanta\Adapter;

use Pagerfanta\Adapter\AdapterInterface;

interface SearcherRequestAdapterInterface extends AdapterInterface
{
    public function getSuggestions();

    /**
     * @return array
     */
    public function getAggregations();
}
