<?php

namespace Kunstmaan\AdminBundle\Helper\Toolbar;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DataCollector
{
    /**
     * @var DataCollectionInterface[]
     */
    protected $dataCollectors = [];

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function addDataCollector(DataCollectionInterface $dataCollectors)
    {
        $this->dataCollectors[] = $dataCollectors;
    }

    /**
     * @return DataCollectionInterface[]
     */
    public function getDataCollectors()
    {
        return $this->dataCollectors;
    }
}
