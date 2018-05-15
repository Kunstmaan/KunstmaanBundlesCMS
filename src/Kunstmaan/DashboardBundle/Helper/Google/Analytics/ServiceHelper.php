<?php

namespace Kunstmaan\DashboardBundle\Helper\Google\Analytics;

use Google_AnalyticsService;
use Kunstmaan\DashboardBundle\Helper\Google\ClientHelper;

/**
 * Class ServiceHelper
 */
class ServiceHelper
{
    /** @var Google_AnalyticsService $service */
    private $service;

    /** @var ClientHelper $clientHelper */
    private $clientHelper;

    /**
     * constructor
     *
     * @param ClientHelper $clientHelper
     */
    public function __construct(ClientHelper $clientHelper)
    {
        $this->clientHelper = $clientHelper;
        $this->service = new Google_AnalyticsService($clientHelper->getClient());
    }

    /**
     * @return Google_AnalyticsService $service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return ClientHelper $clientHelper
     */
    public function getClientHelper()
    {
        return $this->clientHelper;
    }
}
