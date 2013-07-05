<?php
namespace Kunstmaan\TranslatorBundle\Tests;

include __DIR__.'/app/AppKernel.php';

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{

    public $container;
    public $kernel;
    public $connection;
    public $em;
    public static $databaseCreated = false;

    public function setUp()
    {
        parent::setUp();
        $this->bootKernel();
    }

    private function bootKernel()
    {
        $this->kernel = new \AppKernel('phpunit', true);
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
