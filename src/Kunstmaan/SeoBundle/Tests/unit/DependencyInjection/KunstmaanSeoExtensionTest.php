<?php

namespace Kunstmaan\RedirectBundle\Tests\DependencyInjection;

use Kunstmaan\SeoBundle\DependencyInjection\KunstmaanSeoExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;

class KunstmaanSeoExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanSeoExtension()];
    }

    public function testSeoRequestCacheMethodCall()
    {
        $this->setDefinition('kunstmaan_seo.twig.extension', new Definition());
        $this->setDefinition('cache.app', new Definition());

        $this->load(['request_cache' => 'cache.app']);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'kunstmaan_seo.twig.extension',
            'setRequestCache',
            [
                new Reference('cache.app'),
            ]
        );
    }

    public function testSeoRequestCacheMethodCallWithNullValue()
    {
        $this->setDefinition('kunstmaan_seo.twig.extension', new Definition());
        $this->setDefinition('cache.app', new Definition());

        $this->load(['request_cache' => null]);

        $this->compile();

        $this->assertFalse($this->container->getDefinition('kunstmaan_seo.twig.extension')->hasMethodCall('setRequestCache'));
    }
}
