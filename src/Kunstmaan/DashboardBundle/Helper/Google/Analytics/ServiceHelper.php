<?php

namespace Kunstmaan\DashboardBundle\Helper\Google\Analytics;

use Google_AnalyticsService;
use Kunstmaan\DashboardBundle\Helper\Google\ClientHelper;

class ServiceHelper
{
    /** @var Google_AnalyticsService */
    private $service;

    /** @var GoogleClientHelper */
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
