<?php

namespace Kunstmaan\AdminListBundle\Tests\Service;

use Box\Spout\Common\Type;
use Kunstmaan\AdminListBundle\AdminList\ExportableInterface;
use Kunstmaan\AdminListBundle\Service\ExportService;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-03-19 at 09:56:53.
 */
class ExportServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExportService
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ExportService;
    }

    /**
     */
    public function testGetSupportedExtensions()
    {
        $extensions = ExportService::getSupportedExtensions();
        $this->assertEquals(['Csv' => 'csv', 'Ods' => 'ods', 'Excel' => 'xlsx'], $extensions);
    }

    /**
     */
    public function testGetDownloadableResponseReturnsStreamedResponseWithExcel()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock('Kunstmaan\AdminListBundle\AdminList\ExportableInterface');

        $response = $this->object->getDownloadableResponse($adminList, Type::XLSX);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }

    /**
     */
    public function testGetDownloadableResponseReturnsStreamedResponseWithOds()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock('Kunstmaan\AdminListBundle\AdminList\ExportableInterface');

        $response = $this->object->getDownloadableResponse($adminList, Type::ODS);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }

    /**
     */
    public function testGetDownloadableResponseReturnsStreamedResponseWithCsv()
    {
        /** @var ExportableInterface $adminList */
        $adminList = $this->createMock('Kunstmaan\AdminListBundle\AdminList\ExportableInterface');

        $response = $this->object->getDownloadableResponse($adminList, Type::CSV);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }
}
