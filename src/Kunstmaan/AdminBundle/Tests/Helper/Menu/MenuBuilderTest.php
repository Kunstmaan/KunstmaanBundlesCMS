<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilderTest extends TestCase
{
    public function setUpMenuBuilderMock(?array $methods): \PHPUnit\Framework\MockObject\MockObject|MenuBuilder
    {
        $menuBuilderMock = $this->getMockBuilder(MenuBuilder::class)
            ->setConstructorArgs([$this->createMock(RequestStack::class)])
            ->onlyMethods($methods ?? [])
            ->getMock()
        ;

        return $menuBuilderMock;
    }

    public function testGetChildrenAndTopChildren()
    {
        $menuBuilderMock = $this->setUpMenuBuilderMock(null);

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

        $menuBuilderMock = $this->setUpMenuBuilderMock(['getChildren']);
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

        $menuBuilderMock = $this->setUpMenuBuilderMock(['getChildren']);
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

        $menuBuilderMock = $this->setUpMenuBuilderMock(['getChildren']);
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

        $menuBuilderMock = $this->setUpMenuBuilderMock(['getChildren']);
        $menuBuilderMock
            ->expects($this->any())
            ->method('getChildren')
            ->will($this->onConsecutiveCalls([$menuItemMock], []))
        ;

        $this->assertNull($menuBuilderMock->getLowestTopChild());
    }

    public function testGetTopChildren()
    {
        /** @var MenuAdaptorInterface $menuAdaptorInterfaceMock */
        $menuAdaptorInterfaceMock = $this->createMock(MenuAdaptorInterface::class);
        $menuAdaptorInterfaceMock
            ->expects($this->once())
            ->method('adaptChildren')
        ;

        $menuBuilderMock = $this->setUpMenuBuilderMock(null);
        $menuBuilderMock->addAdaptMenu($menuAdaptorInterfaceMock);

        $this->assertIsArray($menuBuilderMock->getTopChildren());
    }
}
