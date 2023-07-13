<?php

namespace Kunstmaan\AdminListBundle\Tests\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;
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

    public function testGetSupportedExtensions()
    {
        $extensions = ExportService::getSupportedExtensions();
        $this->assertSame(['Csv' => 'csv', 'Ods' => 'ods', 'Excel' => 'xlsx'], $extensions);
    }

    public function testGetDownloadableResponseReturnsStreamedResponseWithExcel()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock(ExportableInterface::class);

        $response = $this->object->getDownloadableResponse($adminList, ExportService::XLSX);

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    public function testGetDownloadableResponseReturnsStreamedResponseWithOds()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock(ExportableInterface::class);

        $response = $this->object->getDownloadableResponse($adminList, ExportService::ODS);

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    public function testGetDownloadableResponseReturnsStreamedResponseWithCsv()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock(ExportableInterface::class);

        $response = $this->object->getDownloadableResponse($adminList, ExportService::CSV);

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }
}
