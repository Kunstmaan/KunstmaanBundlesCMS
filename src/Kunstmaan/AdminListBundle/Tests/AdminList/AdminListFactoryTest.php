<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use PHPUnit\Framework\TestCase;

class AdminListFactoryTest extends TestCase
{
    /**
     * @var AdminListFactory
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new AdminListFactory();
    }

    public function testCreateList()
    {
        $mockConfig = $this->createMock('Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface');
        $list = $this->object->createList($mockConfig);

        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\AdminList', $list);
    }

    public function testCreateExportList()
    {
        $mockConfig = $this->createMock('Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface');
        $list = $this->object->createExportList($mockConfig);

        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\ExportList', $list);
    }
}
