<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use PHPUnit\Framework\TestCase;

class MenuItemTest extends TestCase
{
    /**
     * @var MenuItem
     */
    protected $object;

    protected function setUp(): void
    {
        /* @var $menuBuilder MenuBuilder */
        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new MenuItem($menuBuilder);
    }

    public function testGetMenu()
    {
        /* @var $menuBuilder MenuBuilder */
        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $object = new MenuItem($menuBuilder);
        $this->assertEquals($menuBuilder, $object->getMenu());
    }

    public function testGetSetLabel()
    {
        $this->object->setLabel('label');
        $this->assertEquals('label', $this->object->getLabel());
    }

    public function testGetSetUniqueId()
    {
        $this->object->setUniqueId('uniqueId');
        $this->assertEquals('uniqueId', $this->object->getUniqueId());
    }

    public function testGetSetRole()
    {
        $this->object->setRole('ROLE_CUSTOM');
        $this->assertEquals('ROLE_CUSTOM', $this->object->getRole());
    }

    public function testGetSetChildren()
    {
        $this->object->setChildren(['test' => 'ok']);
        $this->assertNotEmpty($this->object->getChildren());
        $this->assertNotNull($this->object->getChildren()['test']);
    }

    public function testGetSetOffline()
    {
        $this->object->setOffline(true);
        $this->assertTrue($this->object->getOffline());
        $this->object->setOffline(false);
        $this->assertFalse($this->object->getOffline());
        $this->object->setOffline(true);
        $this->assertTrue($this->object->getOffline());
    }

    public function testGetSetHiddenFromNav()
    {
        $this->object->setHiddenFromNav(true);
        $this->assertTrue($this->object->isHiddenFromNav());
        $this->object->setHiddenFromNav(false);
        $this->assertFalse($this->object->isHiddenFromNav());
        $this->object->setHiddenFromNav(true);
        $this->assertTrue($this->object->isHiddenFromNav());
    }

    public function testGetSetFolder()
    {
        $this->object->setFolder(true);
        $this->assertTrue($this->object->getFolder());
        $this->object->setFolder(false);
        $this->assertFalse($this->object->getFolder());
        $this->object->setFolder(true);
        $this->assertTrue($this->object->getFolder());
    }

    public function testGetSetParent()
    {
        /* @var $menuBuilder MenuBuilder */
        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $parent = new MenuItem($menuBuilder);
        $this->object->setParent($parent);
        $this->assertEquals($parent, $this->object->getParent());
    }

    public function testGetSetRoute()
    {
        $params = ['id' => 5];
        $this->object->setRoute('ARoute', $params);

        $this->assertEquals('ARoute', $this->object->getRoute());
        $this->assertEquals($params, $this->object->getRouteParams());
    }

    public function testGetSetRouteParams()
    {
        $params = ['id' => 1];
        $this->object->setRouteParams($params);
        $this->assertEquals($params, $this->object->getRouteParams());
    }

    public function testGetChildren()
    {
        $child1 = new MenuItem($this->object->getMenu());
        $child1->setAppearInNavigation(true);
        $child2 = new MenuItem($this->object->getMenu());
        $child2->setAppearInNavigation(true);
        $children = [$child1, $child2];

        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $menuBuilder->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue($children));

        /* @var $menuBuilder MenuBuilder */
        $parent = new MenuItem($menuBuilder);
        $result = $parent->getChildren();
        $this->assertCount(2, $result);
        $this->assertEquals($children, $result);
    }

    public function testGetNavigationChildren()
    {
        $child1 = new MenuItem($this->object->getMenu());
        $child1->setAppearInNavigation(true);
        $child2 = new MenuItem($this->object->getMenu());
        $child2->setAppearInNavigation(false);
        $children = [$child1, $child2];

        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $menuBuilder->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue($children));

        /* @var $menuBuilder MenuBuilder */
        $parent = new MenuItem($menuBuilder);
        $result = $parent->getNavigationChildren();
        $this->assertCount(1, $result);
        $this->assertEquals([$child1], $result);
    }

    public function testGetTopChildren()
    {
        $child1 = new MenuItem($this->object->getMenu());
        $child2 = new TopMenuItem($this->object->getMenu());
        $children = [$child1, $child2];

        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $menuBuilder->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue($children));

        /* @var $menuBuilder MenuBuilder */
        $parent = new MenuItem($menuBuilder);
        $result = $parent->getTopChildren();
        $this->assertCount(1, $result);
        $this->assertEquals([$child2], $result);
    }

    public function testAddGetAttributes()
    {
        $attributes = ['attribute1' => 1, 'attribute2' => 2];
        $this->object->addAttributes($attributes);
        $this->assertEquals($attributes, $this->object->getAttributes());
    }

    public function testGetSetActive()
    {
        $this->object->setActive(true);
        $this->assertTrue($this->object->getActive());
    }

    public function testGetSetAppearInNavigation()
    {
        $this->object->setAppearInNavigation(true);
        $this->assertTrue($this->object->getAppearInNavigation());
    }

    public function testGetSetWeight()
    {
        $this->object->setWeight(10);
        $this->assertEquals(10, $this->object->getWeight());
    }
}
