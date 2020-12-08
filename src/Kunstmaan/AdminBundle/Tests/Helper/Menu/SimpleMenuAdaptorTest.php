<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\SimpleMenuAdaptor;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SimpleMenuAdaptorTest extends TestCase
{
    /** @var AuthorizationCheckerInterface (mock) */
    private $authorizationCheckerInterface;

    /** @var array */
    private $menuItems;

    public function setUp(): void
    {
        $this->authorizationCheckerInterface = $this->createMock(AuthorizationCheckerInterface::class);
        $this->menuItems = [];
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|SimpleMenuAdaptor
     */
    public function setUpSimpleMenuAdaptorMock()
    {
        $simpleMenuAdaptorMock = $this->getMockBuilder(SimpleMenuAdaptor::class)
            ->setConstructorArgs([$this->authorizationCheckerInterface, $this->menuItems])
            ->setMethods(null)
            ->getMock()
        ;

        return $simpleMenuAdaptorMock;
    }

    /**
     * @dataProvider provider
     */
    public function testAdaptChildren(?TopMenuItem $parent, ?string $itemParent)
    {
        $this->menuItems[] = ['parent' => 'not_test_route'];

        $this->menuItems[] = [
            'role' => 'some_role',
            'parent' => $itemParent,
        ];

        $this->menuItems[] = [
            'role' => 'some_role',
            'parent' => $itemParent,
            'route' => 'KunstmaanAdminBundle_menu_item',
            'params' => [],
            'label' => 'menu_item',
        ];

        $children = [];

        /** @var MenuBuilder $menuBuilderMock */
        $menuBuilderMock = $this->createMock(MenuBuilder::class);

        /** @var Request $request */
        $request = new Request([], [], ['_route' => 'KunstmaanAdminBundle_menu_item']);

        $this->authorizationCheckerInterface
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->will($this->onConsecutiveCalls(false, true))
        ;
        $simpleMenuAdaptorMock = $this->setUpSimpleMenuAdaptorMock();
        $simpleMenuAdaptorMock->adaptChildren($menuBuilderMock, $children, $parent, $request);

        $this->assertCount(1, $children);
        $this->assertContainsOnlyInstancesOf(TopMenuItem::class, $children);
        $this->assertEquals('menu_item', $children[0]->getLabel());
    }

    public function testHasInterface()
    {
        $simpleMenuAdaptorMock = $this->setUpSimpleMenuAdaptorMock();
        $this->assertInstanceOf(MenuAdaptorInterface::class, $simpleMenuAdaptorMock);
    }

    public function provider()
    {
        /** @var TopMenuItem $parent */
        $parent = $this->createMock(TopMenuItem::class);
        $parent
            ->expects($this->any())
            ->method('getRoute')
            ->willReturn('test_route')
        ;

        return [
            'with no parent' => [null, null],
            'with parent' => [$parent, 'test_route'],
        ];
    }
}
