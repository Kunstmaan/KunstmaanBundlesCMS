<?php

namespace Kunstmaan\NodeBundle\Tests\DependencyInjection;

use Kunstmaan\NodeBundle\DependencyInjection\KunstmaanNodeExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanNodeExtensionTest extends AbstractExtensionTestCase
{
    use ExpectDeprecationTrait;

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanNodeExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('twig.form.resources', []);
        $this->load(['enable_improved_router' => true]);

        $this->assertContainerBuilderHasParameter('twig.form.resources');
        $this->assertContainerBuilderHasParameter('kunstmaan_node.show_add_homepage', true);
        $this->assertContainerBuilderHasParameter('kunstmaan_node.enable_export_page_template', false);
        $this->assertContainerBuilderHasParameter('kunstmaan_node.lock_check_interval', 15);
        $this->assertContainerBuilderHasParameter('kunstmaan_node.lock_threshold', 35);
        $this->assertContainerBuilderHasParameter('kunstmaan_node.lock_enabled', false);
        $this->assertContainerBuilderHasParameter('kunstmaan_node.version_timeout', 3600);
        $this->assertContainerBuilderHasParameter('kunstmaan_node.url_chooser.lazy_increment', 2);
        $this->assertContainerBuilderHasParameter('kunstmaan_node.enable_improved_urlchooser', false);
    }

    /**
     * @group legacy
     */
    public function testImprovedRouterConfigDeprecation()
    {
        $this->expectDeprecation('Since kunstmaan/node-bundle 7.2: Not setting the "kunstmaan_node.enable_improved_router" config to true is deprecated, it will always be true in 8.0.');
        $this->container->setParameter('twig.form.resources', []);
        $this->load();
    }
}
