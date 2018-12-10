<?php

namespace Kunstmaan\MediaBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\MediaBundle\DependencyInjection\Compiler\MediaHandlerCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class MediaHandlerCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MediaHandlerCompilerPass());
    }

    public function testContainerKeys()
    {
        $svc = new Definition();
        $svc->addTag('kunstmaan_media.media_handler');
        $this->setDefinition('kunstmaan_media.media_manager', $svc);

        $svc = new Definition();
        $svc->addTag('kunstmaan_media.icon_font.loader');
        $this->setDefinition('kunstmaan_media.icon_font_manager', $svc);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'kunstmaan_media.media_manager',
            'addHandler',
            [new Reference('kunstmaan_media.media_manager')]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'kunstmaan_media.icon_font_manager',
            'addLoader',
            [new Reference('kunstmaan_media.icon_font_manager'), 'kunstmaan_media.icon_font_manager']
        );
    }

    public function testLiipImageTaggedResolvers()
    {
        $this->setDefinition('Kunstmaan\MediaBundle\Helper\Imagine\CacheManager', new Definition());

        $testResolver = new Definition();
        $testResolver->addTag('liip_imagine.cache.resolver', ['resolver' => 'test']);
        $this->setDefinition('test_resolver', $testResolver);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'Kunstmaan\MediaBundle\Helper\Imagine\CacheManager',
            'addResolver',
            ['test', new Reference('test_resolver')]
        );
    }
}
