<?php
namespace Kunstmaan\DashboardBundle\Command\Helper\Analytics;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\QueryHelper;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractAnalyticsCommandHelper
{

    /** @var GooglequeryHelper $queryHelper */
    protected $query;
    /** @var EntityManager $em */
    protected $em;
    /** @var OutputInterface $output */
    protected $output;

    /**
     * Constructor
     *
     * @param $queryHelper
     * @param $output
     * @param $em
     */
    public function __construct($queryHelper, $output, $em)
    {
        $this->query = $queryHelper;
        $this->output = $output;
        $this->em = $em;
    }

    public abstract function getData(&$overview);

}
