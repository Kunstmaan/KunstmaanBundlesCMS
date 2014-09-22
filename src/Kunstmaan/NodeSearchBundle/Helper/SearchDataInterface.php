<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

interface SearchDataInterface
{
    /**
     * Returns an associative array (key/value pairs) of extra data to add to the search index
     *
     * @return array
     */
    public function getSearchData();
}
