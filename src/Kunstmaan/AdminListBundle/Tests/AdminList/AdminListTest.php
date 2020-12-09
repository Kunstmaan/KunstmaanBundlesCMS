<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\BulkAction\BulkActionInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;
use Kunstmaan\AdminListBundle\AdminList\ListAction\ListActionInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AdminListTest extends TestCase
{
    /** @var AdminList */
    protected $adminList;

    public function setUp(): void
    {
        /** @var AdminListConfiguratorInterface */
        $configurator = $this->createMock(AdminListConfiguratorInterface::class);

        $configurator->method('getFilterBuilder')->willReturn(new FilterBuilder());
        $configurator->method('getFields')->willReturn(['a', 'b']);
        $configurator->method('getExportFields')->willReturn(['c', 'd']);
        $configurator->method('getCount')->willReturn('666');
        $configurator->method('getItems')->willReturn(['item']);
        $configurator->method('getSortFields')->willReturn(['e']);
        $configurator->method('canEdit')->willReturn(true);
        $configurator->method('canAdd')->willReturn(true);
        $configurator->method('canView')->willReturn(true);
        $configurator->method('canDelete')->willReturn(true);
        $configurator->method('canExport')->willReturn(true);
        $configurator->method('getIndexUrl')->willReturn([]);
        $configurator->method('getEditUrlFor')->willReturn([]);
        $configurator->method('getDeleteUrlFor')->willReturn([]);
        $configurator->method('getAddUrlFor')->willReturn([]);
        $configurator->method('getExportUrl')->willReturn([]);
        $configurator->method('getViewUrlFor')->willReturn([]);
        $configurator->method('getValue')->willReturn('test');
        $configurator->method('getStringValue')->willReturn('stringtest');
        $configurator->method('getOrderBy')->willReturn('name');
        $configurator->method('getOrderDirection')->willReturn('up');
        $configurator->method('getItemActions')->willReturn([$this->createMock(ItemActionInterface::class)]);
        $configurator->method('hasItemActions')->willReturn(true);
        $configurator->method('getListActions')->willReturn([$this->createMock(ListActionInterface::class)]);
        $configurator->method('hasListActions')->willReturn(true);
        $configurator->method('getBulkActions')->willReturn([$this->createMock(BulkActionInterface::class)]);
        $configurator->method('hasBulkActions')->willReturn(true);
        $configurator->method('getPagerfanta')->willReturn($this->createMock(Pagerfanta::class));

        $this->adminList = new AdminList($configurator);
    }

    public function testConstructor()
    {
        $configurator = $this->createMock(AdminListConfiguratorInterface::class);

        $configurator->expects($this->once())->method('buildFilters');
        $configurator->expects($this->once())->method('buildFields');
        $configurator->expects($this->once())->method('buildItemActions');
        $configurator->expects($this->once())->method('buildListActions');

        new AdminList($configurator);
    }

    public function testGetConfigurator()
    {
        $this->assertInstanceOf(AdminListConfiguratorInterface::class, $this->adminList->getConfigurator());
    }

    public function testGetFilterBuilder()
    {
        $this->assertInstanceOf(FilterBuilder::class, $this->adminList->getFilterBuilder());
    }

    public function testGetColumns()
    {
        $this->assertContains('a', $this->adminList->getColumns());
    }

    public function testGetExportColumns()
    {
        $this->assertContains('c', $this->adminList->getExportColumns());
    }

    public function testGetCount()
    {
        $this->assertEquals(666, $this->adminList->getCount());
    }

    public function testGetItems()
    {
        $this->assertContains('item', $this->adminList->getItems());
    }

    public function testHasSort()
    {
        $this->assertTrue($this->adminList->hasSort());
        $this->assertTrue($this->adminList->hasSort('e'));
        $this->assertFalse($this->adminList->hasSort('x'));
    }

    public function testHasSortWithoutColumns()
    {
        $configurator = $this->createMock(AdminListConfiguratorInterface::class);
        $configurator->method('getSortFields')->willReturn([]);

        $adminList = new AdminList($configurator);

        $this->assertFalse($adminList->hasSort());
    }

    public function testCanEdit()
    {
        $item = new \stdClass();
        $this->assertTrue($this->adminList->canEdit($item));
    }

    public function testCanAdd()
    {
        $this->assertTrue($this->adminList->canAdd());
    }

    public function testCanView()
    {
        $item = new \stdClass();
        $this->assertTrue($this->adminList->canView($item));
    }

    public function testCanDelete()
    {
        $item = new \stdClass();
        $this->assertTrue($this->adminList->canDelete($item));
    }

    public function testCanExport()
    {
        $this->assertTrue($this->adminList->canExport());
    }

    public function testGetIndexUrl()
    {
        $this->assertTrue(\is_array($this->adminList->getIndexUrl()));
    }

    public function testGetEditUrlFor()
    {
        $item = new \stdClass();
        $this->assertTrue(\is_array($this->adminList->getEditUrlFor($item)));
    }

    public function testGetDeleteUrlFor()
    {
        $item = new \stdClass();
        $this->assertTrue(\is_array($this->adminList->getDeleteUrlFor($item)));
    }

    public function testGetAddUrlFor()
    {
        $this->assertTrue(\is_array($this->adminList->getAddUrlFor([])));
    }

    public function testGetExportUrl()
    {
        $this->assertTrue(\is_array($this->adminList->getExportUrl()));
    }

    public function testGetViewUrl()
    {
        $item = new \stdClass();
        $this->assertTrue(\is_array($this->adminList->getViewUrlFor($item)));
    }

    public function testGetValue()
    {
        $object = new \stdClass();
        $this->assertEquals('test', $this->adminList->getValue($object, 'test'));
    }

    public function testGetStringValue()
    {
        $object = new \stdClass();
        $this->assertEquals('stringtest', $this->adminList->getStringValue($object, 'test'));
    }

    public function testGetOrderBy()
    {
        $this->assertEquals('name', $this->adminList->getOrderBy());
    }

    public function testGetOrderDirection()
    {
        $this->assertEquals('up', $this->adminList->getOrderDirection());
    }

    public function testGetItemActions()
    {
        $itemActions = $this->adminList->getItemActions();
        $this->assertTrue(\is_array($itemActions));
        $this->assertInstanceOf(ItemActionInterface::class, current($itemActions));
    }

    public function testHasItemActions()
    {
        $this->assertTrue($this->adminList->hasItemActions());
    }

    public function testHasListActions()
    {
        $this->assertTrue($this->adminList->hasListActions());
    }

    public function testGetListActions()
    {
        $listActions = $this->adminList->getListActions();
        $this->assertTrue(\is_array($listActions));
        $this->assertInstanceOf(ListActionInterface::class, current($listActions));
    }

    public function testGetBulkActions()
    {
        $bulkActions = $this->adminList->getBulkActions();
        $this->assertTrue(\is_array($bulkActions));
        $this->assertInstanceOf(BulkActionInterface::class, current($bulkActions));
    }

    public function testHasBulkActions()
    {
        $this->assertTrue($this->adminList->hasBulkActions());
    }

    public function testGetPagerfanta()
    {
        $this->assertInstanceOf(Pagerfanta::class, $this->adminList->getPagerfanta());
    }

    public function testBindRequest()
    {
        $configurator = $this->createMock(AdminListConfiguratorInterface::class);

        $configurator->expects($this->once())->method('bindRequest');
        $adminList = new AdminList($configurator);

        $adminList->bindRequest(new Request());
    }
}
