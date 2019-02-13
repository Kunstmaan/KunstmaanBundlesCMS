<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Codeception\Test\Unit;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\BulkAction\BulkActionInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;
use Kunstmaan\AdminListBundle\AdminList\ListAction\ListActionInterface;
use Pagerfanta\Pagerfanta;

/**
 * Class AdminListTest
 */
class AdminListTest extends Unit
{
    /** @var AdminList */
    protected $adminList;

    public function _before()
    {
        /** @var AdminListConfiguratorInterface */
        $configurator = $this->makeEmpty(AdminListConfiguratorInterface::class, [
            'getFilterBuilder' => new FilterBuilder(),
            'getFields' => ['a', 'b'],
            'getExportFields' => ['c', 'd'],
            'getCount' => '666',
            'getItems' => ['item'],
            'getSortFields' => ['e', 'f'],
            'canEdit' => true,
            'canAdd' => true,
            'canView' => true,
            'canDelete' => true,
            'canExport' => true,
            'getIndexUrl' => [],
            'getEditUrlFor' => [],
            'getDeleteUrlFor' => [],
            'getAddUrlFor' => [],
            'getExportUrl' => [],
            'getViewUrlFor' => [],
            'getValue' => 'test',
            'getStringValue' => 'stringtest',
            'getOrderBy' => 'name',
            'getOrderDirection' => 'up',
            'getItemActions' => [$this->makeEmpty(ItemActionInterface::class)],
            'hasItemActions' => true,
            'getListActions' => [$this->makeEmpty(ListActionInterface::class)],
            'hasListActions' => true,
            'getBulkActions' => [$this->makeEmpty(BulkActionInterface::class)],
            'hasBulkActions' => true,
            'getPagerfanta' => $this->makeEmpty(Pagerfanta::class),
        ]);

        $this->adminList = new AdminList($configurator);
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
        $this->assertEquals(2, $this->adminList->hasSort());
        $this->assertTrue($this->adminList->hasSort('e'));
    }

    public function testCanEdit()
    {
        $item = new \stdClass();
        $this->assertTrue($this->adminList->canEdit($item));
    }

    public function testCanAdd()
    {
        $item = new \stdClass();
        $this->assertTrue($this->adminList->canAdd($item));
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
        $this->assertTrue(is_array($this->adminList->getIndexUrl()));
    }

    public function testGetEditUrlFor()
    {
        $item = new \stdClass();
        $this->assertTrue(is_array($this->adminList->getEditUrlFor($item)));
    }

    public function testGetDeleteUrlFor()
    {
        $item = new \stdClass();
        $this->assertTrue(is_array($this->adminList->getDeleteUrlFor($item)));
    }

    public function testGetAddUrlFor()
    {
        $this->assertTrue(is_array($this->adminList->getAddUrlFor([])));
    }

    public function testGetExportUrl()
    {
        $this->assertTrue(is_array($this->adminList->getExportUrl()));
    }

    public function testGetViewUrl()
    {
        $item = new \stdClass();
        $this->assertTrue(is_array($this->adminList->getViewUrlFor($item)));
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
        $this->assertTrue(is_array($itemActions));
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
        $this->assertTrue(is_array($listActions));
        $this->assertInstanceOf(ListActionInterface::class, current($listActions));
    }

    public function testGetBulkActions()
    {
        $bulkActions = $this->adminList->getBulkActions();
        $this->assertTrue(is_array($bulkActions));
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
}
