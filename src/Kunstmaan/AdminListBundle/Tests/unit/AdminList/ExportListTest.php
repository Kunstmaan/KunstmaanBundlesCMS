<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Codeception\Test\Unit;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\ExportList;

/**
 * Class ExportListTest
 */
class ExportListTest extends Unit
{
    /** @var ExportList */
    protected $exportList;

    public function _before()
    {
        /** @var AdminListConfiguratorInterface */
        $configurator = $this->makeEmpty(ExportListConfiguratorInterface::class,
            [
                'getExportFields' => ['c', 'd'],
                'getIterator' => $this->makeEmpty(\Iterator::class),
                'getStringValue' => 'stringtest',
            ]
        );

        $this->exportList = new ExportList($configurator);
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
