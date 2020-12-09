<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\ExportList;
use PHPUnit\Framework\TestCase;

class ExportListTest extends TestCase
{
    /** @var ExportList */
    protected $exportList;

    public function setUp(): void
    {
        /** @var AdminListConfiguratorInterface */
        $configurator = $this->createMock(ExportListConfiguratorInterface::class);
        $configurator->method('getExportFields')->willReturn(['c', 'd']);
        $configurator->method('getIterator')->willReturn($this->createMock(\Iterator::class));
        $configurator->method('getStringValue')->willReturn('stringtest');

        $this->exportList = new ExportList($configurator);
    }

    public function testConstructor()
    {
        $configurator = $this->createMock(ExportListConfiguratorInterface::class);

        $configurator->expects($this->once())->method('buildFilters');
        $configurator->expects($this->once())->method('buildExportFields');
        $configurator->expects($this->once())->method('buildIterator');

        new ExportList($configurator);
    }

    public function testGetExportColumns()
    {
        $this->assertContains('c', $this->exportList->getExportColumns());
    }

    public function testGetIterator()
    {
        $this->assertInstanceOf(\Iterator::class, $this->exportList->getIterator());
    }

    public function testGetStringValue()
    {
        $object = new \stdClass();
        $this->assertEquals('stringtest', $this->exportList->getStringValue($object, 'test'));
    }
}
