<?php
namespace Kunstmaan\TranslatorBundle\Tests;

use AppKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

include __DIR__ . '/app/AppKernel.php';

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var AppKernel
     */
    public $kernel;

    public $connection;
    public $em;
    public static $databaseCreated = false;

    public function setUp()
    {
        $this->bootKernel();
    }

    private function bootKernel()
    {
        $this->kernel = new AppKernel('phpunit', true);
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }
}
