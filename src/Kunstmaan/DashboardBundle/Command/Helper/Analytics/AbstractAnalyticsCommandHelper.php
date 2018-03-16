<?php

namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ConfigHelper;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\QueryHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractAnalyticsCommandHelper
 */
abstract class AbstractAnalyticsCommandHelper
{
    /** @var ConfigHelper $configHelper */
    protected $configHelper;

    /** @var QueryHelper $queryHelper */
    protected $query;

    /** @var EntityManager $em */
    protected $em;

    /** @var OutputInterface $output */
    protected $output;

    /**
     * Constructor
     *
     * @param $configHelper
     * @param $queryHelper
     * @param $output
     * @param $em
     */
    public function __construct($configHelper, $queryHelper, $output, $em)
    {
        $this->configHelper = $configHelper;
        $this->query = $queryHelper;
        $this->output = $output;
        $this->em = $em;
    }

    /**
     * @param AnalyticsOverview $overview
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function getTimestamps(AnalyticsOverview $overview)
    {
        // if yearoverview set the begin time to the first day of this year
        $profileStartDate = explode('T', $this->configHelper->getActiveProfile()['created'])[0];
        if ($overview->getUseYear()) {
            $begin_date = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y')));
            $begin = strtotime($profileStartDate) > strtotime($begin_date) ? date('Y-m-d', strtotime($profileStartDate)) : $begin_date;
        } else {
            // check if timespan is't more than existence of the profile; if so, use the creation time in stead of the timespan time
            $begin = strtotime($profileStartDate) > strtotime('-'.$overview->getTimespan().' days') ? date(
                'Y-m-d',
                strtotime($profileStartDate)
            ) : date('Y-m-d', strtotime('-'.$overview->getTimespan().' days'));
        }
        // set the end time
        $end = date('Y-m-d', strtotime('-'.$overview->getStartOffset().' days'));

        return ['begin' => $begin, 'end' => $end];
    }

    /**
     * Get the extra data for an overview, can be overridden
     *
     * @param AnalyticsOverview $overview
     *
     * @return array
     */
    protected function getExtra(AnalyticsOverview $overview)
    {
        $extra = [];

        // add segment
        if ($overview->getSegment()) {
            $extra['segment'] = $overview->getSegment()->getQuery();
        }

        return $extra;
    }

    /**
     * Executes the query
     *
     * @param AnalyticsOverview $overview
     * @param                   $metrics
     *
     * @return mixed
     * @throws \Exception
     */
    protected function executeQuery(AnalyticsOverview $overview, $metrics)
    {
        $timestamps = $this->getTimestamps($overview);
        $extra = $this->getExtra($overview);
        // execute query
        $results = $this->query->getResultsByDate(
            $timestamps['begin'],
            $timestamps['end'],
            $metrics,
            $extra
        );

        return $results->getRows();
    }

    public abstract function getData(AnalyticsOverview $overview);

}
