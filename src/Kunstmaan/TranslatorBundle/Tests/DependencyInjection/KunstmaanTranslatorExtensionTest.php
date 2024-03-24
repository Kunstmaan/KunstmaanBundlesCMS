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
        $this->extension->load(['kuma_translator' => ['managed_locales' => ['nl']]], $container);
        $this->assertTrue($container->getParameter('kuma_translator.enabled'));
    }

    public function testDisabled()
    {
        $container = $this->getContainer();
        $this->extension->load(['kuma_translator' => ['enabled' => false]], $container);
        $this->assertFalse($container->hasParameter('kuma_translator.enabled'));
    }

    protected function getExtension(): KunstmaanTranslatorExtension
    {
        return new KunstmaanTranslatorExtension();
    }

    private function getContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.root_dir', 'src/Kunstmaan/TranslatorBundle');
        $container->setParameter('kernel.project_dir', 'src/Kunstmaan/TranslatorBundle');
        $container->setParameter('kernel.bundles', [new \Kunstmaan\TranslatorBundle\KunstmaanTranslatorBundle()]);
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kunstmaan_admin.default_locale', 'en');

        return $container;
    }
}
