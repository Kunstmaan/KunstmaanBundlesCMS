<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class MenuBuilderTest
 */
class MenuBuilderTest extends TestCase
{
    /**
     * @var ContainerInterface (mock)
     */
    protected $container;

    protected function setUp()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->container = $container;
    }

    /**
     * @param ContainerInterface $container
     * @param array|null         $methods
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|MenuBuilder
     */
    public function setUpMenuBuilderMock(ContainerInterface $container, ?array $methods)
    {
        $menuBuilderMock = $this->getMockBuilder(MenuBuilder::class)
            ->setConstructorArgs([$container])
            ->setMethods($methods)
            ->getMock()
        ;

        return $menuBuilderMock;
    }

    public function testGetChildrenAndTopChildren()
    {
        $stack = $this->createMock(RequestStack::class);

        $this->container->expects($this->any())->method('get')->willReturn($stack);

        $menuBuilderMock = $this->setUpMenuBuilderMock($this->container, null);

        /** @var MenuAdaptorInterface $menuAdaptorInterfaceMock */
        $menuAdaptorInterfaceMock = $this->createMock(MenuAdaptorInterface::class);
        $menuAdaptorInterfaceMock
            ->expects($this->exactly(2))
            ->method('adaptChildren')
        ;

        $menuBuilderMock->addAdaptMenu($menuAdaptorInterfaceMock);

        $menuItemMock = $this->createMock(MenuItem::class);
        $this->assertIsArray($menuBuilderMock->getChildren($menuItemMock));
        $this->assertIsArray($menuBuilderMock->getChildren());
    }

    public function testGetCurrentAndBreadCrumb()
    {
        $menuItemMock = $this->createMock(MenuItem::class);
        $menuItemMock
            ->expects($this->any())
            ->method('getActive')
            ->willReturn(true)
        ;

        $menuBuilderMock = $this->setUpMenuBuilderMock($this->container, ['getChildren']);
        $menuBuilderMock
            ->expects($this->any())
            ->method('getChildren')
            ->will($this->onConsecutiveCalls([$menuItemMock], []))
        ;

        $current = $menuBuilderMock->getCurrent();
        $this->assertInstanceOf(MenuItem::class, $current);
        $this->assertContainsOnlyInstancesOf(MenuItem::class, $menuBuilderMock->getBreadCrumb());
    }

    public function testGetLowestTopChildWithCurrentTopMenuItem()
    {
        $menuItemMock = $this->createMock(TopMenuItem::class);
        $menuItemMock
            ->expects($this->any())
            ->method('getActive')
            ->willReturn(true)
        ;

        $menuBuilderMock = $this->setUpMenuBuilderMock($this->container, ['getChildren']);
        $menuBuilderMock
            ->expects($this->any())
            ->method('getChildren')
            ->will($this->onConsecutiveCalls([$menuItemMock], []))
        ;

        $this->assertInstanceOf(TopMenuItem::class, $menuBuilderMock->getLowestTopChild());
    }

    public function testGetLowestTopChildWithMenuItem()
    {
        $menuItemMock = $this->createMock(MenuItem::class);
        $menuItemMock
            ->expects($this->any())
            ->method('getActive')
            ->willReturn(true)
        ;
        $menuItemMock
            ->expects($this->once())
            ->method('getParent')
            ->willReturn($this->createMock(TopMenuItem::class))
        ;

        $menuBuilderMock = $this->setUpMenuBuilderMock($this->container, ['getChildren']);
        $menuBuilderMock
            ->expects($this->any())
            ->method('getChildren')
            ->will($this->onConsecutiveCalls([$menuItemMock], []))
        ;

        $this->assertInstanceOf(TopMenuItem::class, $menuBuilderMock->getLowestTopChild());
    }

    public function testGetLowestTopChildWithCurrentNull()
    {
        $menuItemMock = $this->createMock(TopMenuItem::class);
        $menuItemMock
            ->expects($this->any())
            ->method('getActive')
            ->willReturn(false)
        ;

        $menuBuilderMock = $this->setUpMenuBuilderMock($this->container, ['getChildren']);
        $menuBuilderMock
            ->expects($this->any())
            ->method('getChildren')
            ->will($this->onConsecutiveCalls([$menuItemMock], []))
        ;

        $this->assertNull($menuBuilderMock->getLowestTopChild());
    }

    public function testGetTopChildren()
    {
        $stack = $this->createMock(RequestStack::class);

        $this->container->expects($this->any())->method('get')->willReturn($stack);

        $menuBuilderMock = $this->setUpMenuBuilderMock($this->container, null);

        /** @var MenuAdaptorInterface $menuAdaptorInterfaceMock */
        $menuAdaptorInterfaceMock = $this->createMock(MenuAdaptorInterface::class);
        $menuAdaptorInterfaceMock
            ->expects($this->once())
            ->method('adaptChildren')
        ;

        $menuBuilderMock = $this->setUpMenuBuilderMock($this->container, null);
        $menuBuilderMock->addAdaptMenu($menuAdaptorInterfaceMock);

        $this->assertIsArray($menuBuilderMock->getTopChildren());
    }
}
