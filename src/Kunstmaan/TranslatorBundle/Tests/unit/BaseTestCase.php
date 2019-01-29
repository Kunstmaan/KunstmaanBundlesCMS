<?php

namespace Kunstmaan\TranslatorBundle\Tests\unit;

use AppKernel;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\Persister\Doctrine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

include __DIR__ . '/app/AppKernel.php';

/**
 * Class BaseTestCase
 *
 * @deprecated
 */
abstract class BaseTestCase extends TestCase
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
        $fixtures = __DIR__ . '/files/fixtures.yml';
        $em = $this->kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $loader = new NativeLoader();
        $objects = $loader->loadFile($fixtures)->getObjects();
        foreach($objects as $object) {
            $em->persist($object);
        }
        $em->flush();
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
