<?php

namespace Kunstmaan\AdminListBundle\Tests\Service;

use Box\Spout\Common\Type;
use Kunstmaan\AdminListBundle\AdminList\ExportableInterface;
use Kunstmaan\AdminListBundle\Service\ExportService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

class ExportServiceTest extends TestCase
{
    /**
     * @var ExportService
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ExportService($this->createMock(Environment::class), new Translator('nl'));
    }

    public function testConstructorInvalidTranslatorlass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument 2 passed to "Kunstmaan\AdminListBundle\Service\ExportService::__construct" must be of the type "Symfony\Component\Translation\TranslatorInterface" or "Symfony\Contracts\Translation\TranslatorInterface", "stdClass" given');

        new ExportService($this->createMock(Environment::class), new \stdClass());
    }

    public function testGetSupportedExtensions()
    {
        $extensions = ExportService::getSupportedExtensions();
        $this->assertEquals(['Csv' => 'csv', 'Ods' => 'ods', 'Excel' => 'xlsx'], $extensions);
    }

    public function testGetDownloadableResponseReturnsStreamedResponseWithExcel()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock('Kunstmaan\AdminListBundle\AdminList\ExportableInterface');

        $response = $this->object->getDownloadableResponse($adminList, Type::XLSX);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }

    public function testGetDownloadableResponseReturnsStreamedResponseWithOds()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock('Kunstmaan\AdminListBundle\AdminList\ExportableInterface');

        $response = $this->object->getDownloadableResponse($adminList, Type::ODS);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }

    public function testGetDownloadableResponseReturnsStreamedResponseWithCsv()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock('Kunstmaan\AdminListBundle\AdminList\ExportableInterface');

        $response = $this->object->getDownloadableResponse($adminList, Type::CSV);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }
}
