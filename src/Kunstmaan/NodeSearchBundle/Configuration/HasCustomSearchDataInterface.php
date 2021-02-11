<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

interface HasCustomSearchDataInterface
{
    /**
     * @return array
     */
    public function getCustomSearchData(array $doc);
}
