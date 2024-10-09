<?php

namespace Kunstmaan\NodeBundle\Tests\DependencyInjection;

use Kunstmaan\NodeBundle\DependencyInjection\KunstmaanNodeExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanNodeExtensionTest extends AbstractExtensionTestCase
{
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
        $this->load();

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
}
