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

    /**
     * DataCollector constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param DataCollectionInterface $dataCollectors
     */
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
