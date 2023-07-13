<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\ExportList;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface;
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
        /* @var AdminListConfiguratorInterface $mockConfig */
        $mockConfig = $this->createMock(AdminListConfiguratorInterface::class);
        $list = $this->object->createList($mockConfig);

        $this->assertInstanceOf(AdminList::class, $list);
    }

    public function testCreateExportList()
    {
        /* @var ExportListConfiguratorInterface $mockConfig */
        $mockConfig = $this->createMock(ExportListConfiguratorInterface::class);
        $list = $this->object->createExportList($mockConfig);

        $this->assertInstanceOf(ExportList::class, $list);
    }
}
