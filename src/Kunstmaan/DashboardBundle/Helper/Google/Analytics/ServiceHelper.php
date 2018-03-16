<?php

namespace Kunstmaan\DashboardBundle\Helper\Google\Analytics;

use Kunstmaan\DashboardBundle\Helper\Google\ClientHelper;

/**
 * Class ServiceHelper
 */
class ServiceHelper
{
    /** @var \Google_Service_Analytics $service */
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
        $this->service = new \Google_Service_Analytics($clientHelper->getClient());
    }

    /**
     * @return \Google_Service_Analytics $service
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
