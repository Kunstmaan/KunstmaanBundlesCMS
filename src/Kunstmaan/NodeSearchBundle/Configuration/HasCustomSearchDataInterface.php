<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

/**
 * Interface HasCustomSearchDataInterface
 *
 * @package Kunstmaan\NodeSearchBundle\Configuration
 */
interface HasCustomSearchDataInterface
{
    /**
     * @param array $doc
     *
     * @return mixed
     */
    public function getCustomSearchData(array $doc);
}
