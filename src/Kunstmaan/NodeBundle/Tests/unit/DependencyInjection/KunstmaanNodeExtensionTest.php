<?php

namespace Kunstmaan\NodeBundle\Tests\DependencyInjection;

use Kunstmaan\NodeBundle\DependencyInjection\KunstmaanNodeExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanNodeExtensionTest
 */
class KunstmaanNodeExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
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
        $this->assertContainerBuilderHasParameter('kunstmaan_node.slugrouter.class', 'Kunstmaan\NodeBundle\Router\SlugRouter');
        $this->assertContainerBuilderHasParameter('kunstmaan_node.sluglistener.class', 'Kunstmaan\NodeBundle\EventListener\SlugListener');
        $this->assertContainerBuilderHasParameter('kunstmaan_node.helper.url.class', 'Kunstmaan\NodeBundle\Helper\URLHelper');
        $this->assertContainerBuilderHasParameter('kunstmaan_node.url_replace.twig.class', 'Kunstmaan\NodeBundle\Twig\UrlReplaceTwigExtension');
        $this->assertContainerBuilderHasParameter('kunstmaan_node.url_chooser.lazy_increment', 2);
        $this->assertContainerBuilderHasParameter('kunstmaan_multi_domain.url_replace.controller.class', 'Kunstmaan\NodeBundle\Controller\UrlReplaceController');
    }
}
