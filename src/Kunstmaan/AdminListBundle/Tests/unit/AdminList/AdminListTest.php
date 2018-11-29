<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use ArrayIterator;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\Tests\unit\Model\ConcreteConfigurator;
use Kunstmaan\MenuBundle\Entity\MenuItem;
use PHPUnit_Framework_TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class PrivateObject
{
    protected $name = 'delboy1978uk';
}

class PublicObject extends PrivateObject
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

/**
 * Class AdminListTest
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
        $this->assertEquals('fake pagerfanta', $adminList->getPagerfanta());
        $this->assertInstanceOf(ArrayIterator::class, $adminList->getIterator());
        $this->assertTrue($adminList->canAdd());
        $this->assertTrue($adminList->canEdit($item));
        $this->assertTrue($adminList->canDelete($item));
        $this->assertFalse($adminList->canExport());
        $this->assertFalse($adminList->canView($item));
        $this->assertFalse($adminList->hasSort());
        $this->assertFalse($adminList->hasSort('non-exitant-var-name'));

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

    public function testGetValue()
    {
        $config = new ConcreteConfigurator();
        $adminList = new AdminList($config);

        $em = $this->createMock(EntityManager::class);
        $meta = $this->createMock(ClassMetadata::class);

        $collection = new PersistentCollection($em, $meta, new ArrayCollection([
            new PublicObject(),
        ]));

        $object = new stdClass();
        $object->name = 'delboy1978uk';
        $value = $adminList->getValue($object, 'name');
        $this->assertEquals('delboy1978uk', $value);

        $array = [
            'name' => 'delboy1978uk',
        ];
        $value = $adminList->getValue($array, 'name');
        $this->assertEquals('delboy1978uk', $value);
        $this->assertEquals('', $adminList->getValue($array, 'missing'));

        $private = new PrivateObject();
        $this->assertEquals('undefined function [get/is/has]name()', $adminList->getValue($private, 'name'));

        $date = new DateTime('2014-09-18 22:00:00');
        $array = [
            'key' => $date,
            'array' => [
                'random',
                'strings',
            ],
            'bool' => true,
            'string' => 'strings!',
            'persistent' => $collection,
        ];
        $value = $adminList->getStringValue($array, 'key');
        $this->assertEquals('2014-09-18 22:00:00', $value);
        $this->assertEquals('random, strings', $adminList->getStringValue($array, 'array'));
        $this->assertEquals('true', $adminList->getStringValue($array, 'bool'));
        $this->assertEquals('strings!', $adminList->getStringValue($array, 'string'));
        $this->assertEquals('delboy1978uk', $adminList->getStringValue($array, 'persistent'));

        $collection = new PersistentCollection($em, $meta, new ArrayCollection([]));

        $array = [
            'persistent' => $collection,
        ];
        $this->assertEquals('', $adminList->getStringValue($array, 'persistent'));
    }

    public function testBindRequest()
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->any())->method('has')->willReturn(true);
        $session->expects($this->any())->method('get')->willReturn([
            'page' => 1,
            'orderBy' => 'id',
            'orderDirection' => 'ASC',
        ]);
        $request = new Request();
        $request->setSession($session);
        $request->query->add([
            '_route' => 'some-route',
        ]);
        $config = new ConcreteConfigurator();
        $adminList = new AdminList($config);
        $adminList->bindRequest($request);

        $this->assertEquals('id', $adminList->getOrderBy());
        $this->assertEquals('ASC', $adminList->getOrderDirection());
        $this->assertEquals(1, $adminList->getConfigurator()->getPage());
    }
}
