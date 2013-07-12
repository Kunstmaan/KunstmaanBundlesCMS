<?php
namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Tests\BaseTestCase;
use Symfony\Component\Finder\Finder;

class ExporterTest extends BaseTestCase
{

    private $exporter;
    private $translationRepository;

    public function setUp()
    {
        parent::setUp();
        $this->exporter = $this->getContainer()->get('kunstmaan_translator.service.exporter.exporter');
    }

    /**
     * @group exporter
     */
    public function testGetExporterByExtension()
    {
        $exporter = $this->exporter->getExporterByExtension('yml');
        $this->assertInstanceOf('\Kunstmaan\TranslatorBundle\Service\Exporter\YamlFileExporter', $exporter);
    }

    /**
     * @group exporter
     * @expectedException \Exception
     */
    public function testGetExporterByExtensionNonFound()
    {
        $exporter = $this->exporter->getExporterByExtension('exotic');

    }


}
