<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Helper\GoogleAnalyticsHelper;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractAnalyticsCommandHelper {

    /** @var GoogleAnalyticsHelper $analyticsHelper */
    protected $analyticsHelper;
    /** @var EntityManager $em */
    protected $em;
    /** @var OutputInterface $output */
    protected $output;

    /**
     * Constructor
     *
     * @param $analyticsHelper
     * @param $output
     * @param $em
     */
    public function __construct($analyticsHelper, $output, $em) {
        $this->analyticsHelper = $analyticsHelper;
        $this->output = $output;
        $this->em = $em;
    }

    public abstract function getData(&$overview);

}
