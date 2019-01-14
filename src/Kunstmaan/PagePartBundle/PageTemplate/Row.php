<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

/**
 * Definition of a row in a page template
 */
class Row
{
    /**
     * @var Region[]
     */
    protected $regions;

    /**
     * @param Region[] $regions
     */
    public function __construct(array $regions)
    {
        $this->setRegions($regions);
    }

    /**
     * @return Region[]
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @param array $regions
     */
    public function setRegions(array $regions)
    {
        $this->regions = $regions;
    }
}
