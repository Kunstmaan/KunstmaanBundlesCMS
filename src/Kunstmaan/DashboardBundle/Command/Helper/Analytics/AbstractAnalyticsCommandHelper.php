<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\QueryHelper;
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
    protected function getTimestamps(&$overview) {
        // if yearoverview set the begin time to the first day of this year
        if ($overview->getUseYear()) {
            $begin = date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y')));
        } else {
            // check if timespan is't more than existence of the profile; if so, use the creation time in stead of the timespan time
            $profileStartDate = explode('T', $this->configHelper->getActiveProfile()->created)[0];
            $begin = strtotime($profileStartDate) > strtotime('-' . $overview->getTimespan() . ' days') ? date('Y-m-d', strtotime($profileStartDate)) : date('Y-m-d', strtotime('-' . $overview->getTimespan() . ' days'));
        }
        // set the end time
        $end = date('Y-m-d', strtotime('-' . $overview->getStartOffset() . ' days'));

        return array('begin' => $begin, 'end' => $end);
    }

    public abstract function getData(&$overview);

}
