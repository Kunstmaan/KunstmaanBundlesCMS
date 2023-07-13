<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Exporter;

use Kunstmaan\TranslatorBundle\Service\Command\Exporter\YamlFileExporter;
use Kunstmaan\TranslatorBundle\DependencyInjection\Compiler\KunstmaanTranslatorCompilerPass;
use Kunstmaan\TranslatorBundle\Service\Command\Exporter\Exporter;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ExporterTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new KunstmaanTranslatorCompilerPass());
    }

    private function registerDefinations()
    {
        $svcId = 'kunstmaan_translator.service.exporter.exporter';
        $svc = new Definition(Exporter::class);
        $svc->setPublic(true);
        $this->setDefinition($svcId, $svc);

        $svcId = 'kunstmaan_translator.service.exporter.yaml';
        $svc = new Definition(YamlFileExporter::class);
        $svc->addTag('translation.exporter', ['alias' => 'yml']);
        $this->setDefinition($svcId, $svc);
    }

    public function testExportServiceExists()
    {
        $this->registerDefinations();
        $this->compile();

        $this->assertContainerBuilderHasService('kunstmaan_translator.service.exporter.exporter', Exporter::class);
    }

    /**
     * @group exporter
     */
    public function testGetExporterByExtension()
    {
        $this->registerDefinations();
        $this->compile();

        $exporterService = $this->container->get('kunstmaan_translator.service.exporter.exporter');

        /** @var Exporter $exporter */
        $exporter = $exporterService->getExporterByExtension('yml');
        $this->assertInstanceOf('\\' . YamlFileExporter::class, $exporter);
    }

    /**
     * @group exporter
     */
    public function testGetExporterByExtensionNonFound()
    {
        $this->expectException(\Exception::class);
        $this->expectException('\Exception');
        $this->expectExceptionMessage('No exotic file exporter found or defined.');
        $this->registerDefinations();
        $this->compile();

        $exporterService = $this->container->get('kunstmaan_translator.service.exporter.exporter');

        /** @var Exporter $exporter */
        $exporter = $exporterService->getExporterByExtension('exotic');
    }
}
