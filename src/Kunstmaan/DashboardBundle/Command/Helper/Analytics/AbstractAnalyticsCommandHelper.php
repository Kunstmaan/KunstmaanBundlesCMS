<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractAnalyticsCommandHelper
{

    /** @var ConfigHelper $configHelper */
    protected $configHelper;
    /** @var GooglequeryHelper $queryHelper */
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
     * Constructor
     *
     * @param AnalyticsOverview $overview
     *
     * @return array
     */
    protected function getTimestamps(AnalyticsOverview &$overview) {
        // if yearoverview set the begin time to the first day of this year
        $profileStartDate = explode('T', $this->configHelper->getActiveProfile()['created'])[0];
        if ($overview->getUseYear()) {
            $begin_date = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y')));
            $begin = strtotime($profileStartDate) > strtotime($begin_date) ? date('Y-m-d', strtotime($profileStartDate)) : $begin_date;
        } else {
            // check if timespan is't more than existence of the profile; if so, use the creation time in stead of the timespan time
            $begin = strtotime($profileStartDate) > strtotime('-' . $overview->getTimespan() . ' days') ? date('Y-m-d', strtotime($profileStartDate)) : date('Y-m-d', strtotime('-' . $overview->getTimespan() . ' days'));
        }
        // set the end time
        $end = date('Y-m-d', strtotime('-' . $overview->getStartOffset() . ' days'));

        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * get the extra data for an overview, can be overridden
     *
     * @return array
     */
    protected function getExtra(AnalyticsOverview $overview) {
        $extra = array();

        // add segment
        if ($overview->getSegment()) {
            $extra['segment'] = $overview->getSegment()->getQuery();
        }

        return $extra;
    }

    /**
     * Executes the query
     *
     * @return array the resultset
     */
    protected function executeQuery(AnalyticsOverview $overview, $metrics) {
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

    public abstract function getData(&$overview);

}
