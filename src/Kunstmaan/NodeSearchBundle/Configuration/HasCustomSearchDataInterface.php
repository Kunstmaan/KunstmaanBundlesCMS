<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

interface HasCustomSearchDataInterface
{
    /**
     * @param array $doc
     *
     * @return array
     */
    public function getCustomSearchData(array $doc);
}
