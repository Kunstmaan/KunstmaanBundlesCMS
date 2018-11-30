<?php

namespace Kunstmaan\DashboardBundle\Helper\Google\Analytics;

class QueryHelper
{
    /** @var ServiceHelper */
    private $serviceHelper;

    /** @var ConfigHelper */
    private $configHelper;

    /**
     * constructor
     *
     * @var ServiceHelper
     */
    public function __construct(ServiceHelper $serviceHelper, ConfigHelper $configHelper)
    {
        $this->serviceHelper = $serviceHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * Constructs a Google API query and returns the result
     *
     * @param int    $timespan    Timespan for the data to query in days
     * @param int    $startOffset An offset in days
     * @param string $metrics     The needed metrics
     * @param array  $extra       Extra options suchs as dimentions, sort data, filter data,..
     *
     * @return \Google_GaData result    A data object containing the queried data
     */
    public function getResults($timespan, $startOffset, $metrics, $extra = array())
    {
        $profileId = $this->configHelper->getProfileId();

        return $this->serviceHelper->getService()->data_ga->get(
            'ga:' . $profileId,
            $timespan . 'daysAgo',
            $startOffset . 'daysAgo',
            $metrics,
            $extra
        );
    }

    /**
     * Constructs a Google API query and returns the result
     *
     * @param string $from    Start date for the data to query
     * @param string $to      End date in the past
     * @param string $metrics The needed metrics
     * @param array  $extra   Extra options suchs as dimentions, sort data, filter data,..
     *
     * @return \Google_GaData result    A data object containing the queried data
     */
    public function getResultsByDate($from, $to, $metrics, $extra = array())
    {
        $profileId = $this->configHelper->getProfileId();

        return $this->serviceHelper->getService()->data_ga->get(
            'ga:' . $profileId,
            $from,
            $to,
            $metrics,
            $extra
        );
    }

    /**
     * get the service helper
     *
     *  @return ServiceHelper $serviceHelper
     */
    public function getServiceHelper()
    {
        return $this->serviceHelper;
    }
}
