<?php
namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-21 at 09:05:10.
 */
class MenuItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MenuItem
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        /* @var $menuBuilder MenuBuilder */
        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new MenuItem($menuBuilder);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getMenu
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::__construct
     */
    public function testGetMenu()
    {
        /* @var $menuBuilder MenuBuilder */
        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $object = new MenuItem($menuBuilder);
        $this->assertEquals($menuBuilder, $object->getMenu());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getInternalName
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::setInternalName
     */
    public function testGetSetInternalName()
    {
        $this->object->setInternalName('Internal name');
        $this->assertEquals('Internal name', $this->object->getInternalName());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getRole
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::setRole
     */
    public function testGetSetRole()
    {
        $this->object->setRole('ROLE_CUSTOM');
        $this->assertEquals('ROLE_CUSTOM', $this->object->getRole());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getParent
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::setParent
     */
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

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getRoute
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::setRoute
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getRouteParams
     */
    public function testGetSetRoute()
    {
        $params = array('id' => 5);
        $this->object->setRoute('ARoute', $params);

        $this->assertEquals('ARoute', $this->object->getRoute());
        $this->assertEquals($params, $this->object->getRouteParams());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getRouteParams
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::setRouteParams
     */
    public function testGetSetRouteParams()
    {
        $params = array('id' => 1);
        $this->object->setRouteParams($params);
        $this->assertEquals($params, $this->object->getRouteParams());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getChildren
     */
    public function testGetChildren()
    {
        $child1 = new MenuItem($this->object->getMenu());
        $child1->setAppearInNavigation(true);
        $child2 = new MenuItem($this->object->getMenu());
        $child2->setAppearInNavigation(true);
        $children = array($child1, $child2);


        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $menuBuilder->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue($children));

        /* @var $menuBuilder MenuBuilder */
        $parent = new MenuItem($menuBuilder);
        $result = $parent->getChildren();
        $this->assertEquals(2, count($result));
        $this->assertEquals($children, $result);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getNavigationChildren
     */
    public function testGetNavigationChildren()
    {
        $child1 = new MenuItem($this->object->getMenu());
        $child1->setAppearInNavigation(true);
        $child2 = new MenuItem($this->object->getMenu());
        $child2->setAppearInNavigation(false);
        $children = array($child1, $child2);

        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $menuBuilder->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue($children));

        /* @var $menuBuilder MenuBuilder */
        $parent = new MenuItem($menuBuilder);
        $result = $parent->getNavigationChildren();
        $this->assertEquals(1, count($result));
        $this->assertEquals(array($child1), $result);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getTopChildren
     */
    public function testGetTopChildren()
    {
        $child1 = new MenuItem($this->object->getMenu());
        $child2 = new TopMenuItem($this->object->getMenu());
        $children = array($child1, $child2);

        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $menuBuilder->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue($children));

        /* @var $menuBuilder MenuBuilder */
        $parent = new MenuItem($menuBuilder);
        $result = $parent->getTopChildren();
        $this->assertEquals(1, count($result));
        $this->assertEquals(array($child2), $result);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::addAttributes
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getAttributes
     */
    public function testAddGetAttributes()
    {
        $attributes = array('attribute1' => 1, 'attribute2' => 2);
        $this->object->addAttributes($attributes);
        $this->assertEquals($attributes, $this->object->getAttributes());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getActive
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::setActive
     */
    public function testGetSetActive()
    {
        $this->object->setActive(true);
        $this->assertTrue($this->object->getActive());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getAppearInNavigation
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::setAppearInNavigation
     */
    public function testGetSetAppearInNavigation()
    {
        $this->object->setAppearInNavigation(true);
        $this->assertTrue($this->object->getAppearInNavigation());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::getWeight
     * @covers Kunstmaan\AdminBundle\Helper\Menu\MenuItem::setWeight
     */
    public function testGetSetWeight()
    {
        $this->object->setWeight(10);
        $this->assertEquals(10, $this->object->getWeight());
    }
}
