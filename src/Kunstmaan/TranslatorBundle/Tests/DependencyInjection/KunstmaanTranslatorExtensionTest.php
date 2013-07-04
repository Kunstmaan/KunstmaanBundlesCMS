<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kunstmaan\TranslatorBundle\DependencyInjection\KunstmaanTranslatorExtension;

class KunstmaanVotingExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KunstmaanVotingExtension
     */
    private $extension;

    /**
     * Root name of the configuration
     *
     * @var string
     */
    private $root;

    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
        $this->root      = "kuma_translator";
    }

    public function testEnabledByDefault()
    {
        $container = $this->getContainer();
        $this->extension->load(array( 'kuma_translator' => array('managed_locales' => array('nl'))), $container);
        $this->assertTrue($container->getParameter('kuma_translator.enabled'));
    }

    public function testDisabled()
    {
        $container = $this->getContainer();
        $this->extension->load(array('kuma_translator' => array('enabled' => false)), $container);
        $this->assertFalse($container->hasParameter('kuma_translator.enabled'));
    }

    /**
     * Returns the Configuration to test
     *
     * @return Configuration
     */
    protected function getExtension()
    {
        return new KunstmaanTranslatorExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        $container = new ContainerBuilder;
        $container->setParameter('kernel.root_dir', '');
        $container->setParameter('kernel.bundles', array());

        return $container;
    }
}