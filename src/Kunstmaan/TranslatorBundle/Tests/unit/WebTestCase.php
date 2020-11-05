<?php

namespace Kunstmaan\TranslatorBundle\Tests\unit;

use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class WebTestCase extends BaseWebTestCase
{
    public static function assertRedirect($response, $location)
    {
        self::assertTrue($response->isRedirect(), 'Response is not a redirect, got status code: ' . $response->getStatusCode());
        self::assertEquals('http://localhost' . $location, $response->headers->get('Location'));
    }

    public static function setUpBeforeClass()
    {
        static::deleteTmpDir();
    }

    public static function tearDownAfterClass()
    {
        static::deleteTmpDir();
    }

    protected static function deleteTmpDir()
    {
        if (!file_exists($dir = sys_get_temp_dir() . '/' . static::getVarDir())) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    protected static function getKernelClass()
    {
        require_once __DIR__ . '/app/AppKernel.php';

        return 'Kunstmaan\TranslatorBundle\Tests\app\AppKernel';
    }

    protected static function createKernel(array $options = [])
    {
        $class = self::getKernelClass();

        if (!isset($options['test_case'])) {
            throw new \InvalidArgumentException('The option "test_case" must be set.');
        }

        return new $class(
            static::getVarDir(),
            $options['test_case'],
            isset($options['root_config']) ? $options['root_config'] : 'config.yml',
            isset($options['environment']) ? $options['environment'] : strtolower(static::getVarDir() . $options['test_case']),
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    protected static function getVarDir()
    {
        return 'FB' . substr(strrchr(\get_called_class(), '\\'), 1);
    }

    protected static function loadFixtures(ContainerInterface $container)
    {
        $em = $container->get('doctrine.orm.default_entity_manager');
        $meta = $em->getMetadataFactory()->getAllMetadata();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->dropSchema($meta);
        $tool->createSchema($meta);

        // insert fixtures
        $fixtures = __DIR__ . '/files/fixtures.yml';
        $loader = new NativeLoader();
        $objects = $loader->loadFile($fixtures)->getObjects();
        foreach ($objects as $object) {
            $em->persist($object);
        }
        $em->flush();
    }
}
