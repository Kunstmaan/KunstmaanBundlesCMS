<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kunstmaan\SearchBundle\DependencyInjection\KunstmaanSearchExtension;

class KunstmaanSearchExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KunstmaanSearchExtension
     */
    private $extension;

    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
    }

    public function testGetConfig()
    {
        $container = $this->getContainer();
        $this->extension->load(array(array()), $container);
        $this->assertTrue($container->hasParameter('analyzer_languages'));
        $this->assertTrue(is_array($container->getParameter('analyzer_languages')));

        $analyzers = $container->getParameter('analyzer_languages');
        $this->assertTrue(array_key_exists('ar', $analyzers));
        $this->assertEquals('arabic', $analyzers['ar']['analyzer']);
    }

    /**
     * Returns the Configuration to test
     *
     * @return Configuration
     */
    protected function getExtension()
    {
        return new KunstmaanSearchExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        return new ContainerBuilder();
    }
}
