<?php

namespace Kunstmaan\TranslatorBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\TranslatorBundle\DependencyInjection\Compiler\KunstmaanTranslatorCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class KunstmaanTranslatorCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new KunstmaanTranslatorCompilerPass());
    }

    public function testContainerKeys()
    {
        $svcId = 'kunstmaan_translator.service.importer.importer';
        $svc = new Definition();
        $svc->addTag('translation.loader', ['alias' => 'someAlias', 'legacy-alias' => 'someLegacyAlias']);
        $svc->addTag('translation.exporter', ['alias' => 'someAlias']);
        $this->setDefinition($svcId, $svc);

        $this->setDefinition('kunstmaan_translator.service.translator.translator', new Definition(null, [
            'container', 'formatter', 'default_locale', 'replaceMe', 'options',
        ]));

        $this->setDefinition('kunstmaan_translator.service.exporter.exporter', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addLoader',
            ['someAlias', new Reference($svcId)]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $svcId,
            'addLoader',
            ['someLegacyAlias', new Reference($svcId)]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'kunstmaan_translator.service.exporter.exporter',
            'setExporters',
            [['someAlias' => new Reference($svcId)]]
        );
    }
}
