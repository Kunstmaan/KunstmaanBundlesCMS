<?php

namespace Kunstmaan\TranslatorBundle\Tests\DependencyInjection;

use Kunstmaan\TranslatorBundle\DependencyInjection\KunstmaanTranslatorExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KunstmaanTranslatorExtensionTest extends TestCase
{
    /**
     * @var KunstmaanTranslatorExtension
     */
    private $extension;

    public function setUp(): void
    {
        parent::setUp();

        $this->extension = $this->getExtension();
    }

    public function testEnabledByDefault()
    {
        $container = $this->getContainer();
        $this->extension->load(array('kuma_translator' => array('managed_locales' => array('nl'))), $container);
        $this->assertTrue($container->getParameter('kuma_translator.enabled'));
    }

    public function testDisabled()
    {
        $container = $this->getContainer();
        $this->extension->load(array('kuma_translator' => array('enabled' => false)), $container);
        $this->assertFalse($container->hasParameter('kuma_translator.enabled'));
    }

    /**
     * @return KunstmaanTranslatorExtension
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
        $container = new ContainerBuilder();
        $container->setParameter('kernel.root_dir', 'src/Kunstmaan/TranslatorBundle');
        $container->setParameter('kernel.project_dir', 'src/Kunstmaan/TranslatorBundle');
        $container->setParameter('kernel.bundles', array(new \Kunstmaan\TranslatorBundle\KunstmaanTranslatorBundle()));
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kunstmaan_admin.default_locale', 'en');

        return $container;
    }
}
