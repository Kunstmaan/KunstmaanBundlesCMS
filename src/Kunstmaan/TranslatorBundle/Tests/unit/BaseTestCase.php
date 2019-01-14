<?php

namespace Kunstmaan\TranslatorBundle\Tests\unit;

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

        $em = $this->kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $meta = $em->getMetadataFactory()->getAllMetadata();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->dropSchema($meta);
        $tool->createSchema($meta);

        // insert fixtures
        $fixtures = array(__DIR__ . '/files/fixtures.yml');
        $em = $this->kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $objects = \Nelmio\Alice\Fixtures::load($fixtures, $em);
        $persister = new \Nelmio\Alice\Persister\Doctrine($em);
        $persister->persist($objects);
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
