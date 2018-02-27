<?php
namespace Tests\Kunstmaan\AdminListBundle\AdminList;

use ArrayIterator;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\MenuBundle\Entity\MenuItem;
use Pagerfanta\Pagerfanta;
use PHPUnit_Framework_TestCase;

class ConcreteConfigurator extends AbstractAdminListConfigurator
{
    /**
     * @return string
     */
    public function getBundleName()
    {
        return 'xyz';
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return 'Xyz';
    }

    /**
     * @return mixed
     */
    public function buildFields()
    {
        return true;
    }

    /**
     * @param array|object $item
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return [
            'Xyz' =>  [
                'path' => 'xyz_admin_xyz_edit',
                'params' => [],
            ]
        ];
    }

    /**
     * @param array|object $item
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return [
            'Xyz' =>  [
                'path' => 'xyz_admin_xyz_delete',
                'params' => [],
            ]
        ];
    }

    /**
     * @return int
     */
    public function getCount()
    {
        // TODO: Implement getCount() method.
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return [
            'some' => 'item',
        ];
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
//        return new Pagerfanta();
    }

    /**
     * @return mixed
     */
    public function getIterator()
    {
        return new ArrayIterator();
    }
}

/**
 * Class AdminListTest
 * @package Tests\Kunstmaan\AdminListBundle\AdminList
 */
class AdminListTest extends PHPUnit_Framework_TestCase
{

    public function testStuff()
    {
        $item = new MenuItem();
        $item->setId(666);

        $config = new ConcreteConfigurator();
        $adminList = new AdminList($config);

        $this->assertInstanceOf(ConcreteConfigurator::class, $adminList->getConfigurator());
        $this->assertInstanceOf(FilterBuilder::class, $adminList->getFilterBuilder());
        $this->assertCount(0, $adminList->getColumns());
        $this->assertCount(0, $adminList->getExportColumns());
        $this->assertEquals(0, $adminList->getCount());
        $this->assertInstanceOf(ArrayIterator::class, $adminList->getIterator());
        $this->assertTrue($adminList->canAdd());
        $this->assertTrue($adminList->canEdit($item));
        $this->assertTrue($adminList->canDelete($item));
        $this->assertFalse($adminList->canExport());
        $this->assertFalse($adminList->canView($item));
        $this->assertFalse($adminList->hasSort());

        $url = $adminList->getIndexUrl();
        $this->assertArrayHasKey('path', $url);
        $this->assertArrayHasKey('params', $url);
        $this->assertEquals('xyz_admin_xyz', $url['path']);
        $this->assertEmpty($url['params']);

        $url = $adminList->getExportUrl();
        $this->assertArrayHasKey('path', $url);
        $this->assertArrayHasKey('params', $url);
        $this->assertArrayHasKey('_format', $url['params']);
        $this->assertCount(1, $url['params']);
        $this->assertEquals('xyz_admin_xyz_export', $url['path']);
        $this->assertEquals('csv', $url['params']['_format']);

        $url = $adminList->getAddUrlFor([]);
        $this->assertArrayHasKey('Xyz', $url);
        $this->assertCount(2, $url['Xyz']);
        $this->assertEquals('xyz_admin_xyz_add', $url['Xyz']['path']);
        $this->assertCount(0, $url['Xyz']['params']);

        $url = $adminList->getEditUrlFor($item);
        $this->assertArrayHasKey('Xyz', $url);
        $this->assertCount(2, $url['Xyz']);
        $this->assertEquals('xyz_admin_xyz_edit', $url['Xyz']['path']);
        $this->assertCount(0, $url['Xyz']['params']);

        $url = $adminList->getDeleteUrlFor($item);
        $this->assertArrayHasKey('Xyz', $url);
        $this->assertCount(2, $url['Xyz']);
        $this->assertEquals('xyz_admin_xyz_delete', $url['Xyz']['path']);
        $this->assertCount(0, $url['Xyz']['params']);

        $url = $adminList->getViewUrlFor($item);
        $this->assertCount(2, $url);
        $this->assertArrayHasKey('path', $url);
        $this->assertArrayHasKey('params', $url);
        $this->assertEquals('xyz_admin_xyz_view', $url['path']);
        $this->assertCount(1, $url['params']);
        $this->assertArrayHasKey('id', $url['params']);
        $this->assertEquals(666, $url['params']['id']);

        $items = $adminList->getItems();
        $this->assertArrayHasKey('some', $items);

        $hasBulkActions = $adminList->hasBulkActions();
        $bulkActions = $adminList->getBulkActions();
        $this->assertFalse($hasBulkActions);
        $this->assertEmpty($bulkActions);

        $hasListActions = $adminList->hasListActions();
        $listActions = $adminList->getListActions();
        $this->assertFalse($hasListActions);
        $this->assertEmpty($listActions);

        $hasItemActions = $adminList->hasItemActions();
        $itemActions = $adminList->getItemActions();
        $this->assertFalse($hasItemActions);
        $this->assertEmpty($itemActions);
    }
}
