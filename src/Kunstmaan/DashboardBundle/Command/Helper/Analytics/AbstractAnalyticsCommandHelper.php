<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

abstract class AbstractAnalyticsCommandHelper {

    /** @var GoogleAnalyticsHelper $analyticsHelper */
    protected $analyticsHelper;
    /** @var EntityManager $em */
    protected $em;
    /** @var OutputInterface $output */
    protected $output;

    // todo inject ?
    public function __construct($analyticsHelper, $output, $em) {
        $this->analyticsHelper = $analyticsHelper;
        $this->output = $output;
        $this->em = $em;
    }

    public abstract function getData(&$overview);

}
