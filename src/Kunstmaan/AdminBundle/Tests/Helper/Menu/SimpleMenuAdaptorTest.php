<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use PHPUnit\Framework\MockObject\MockObject;
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

    private array $menuItems;

    public function setUp(): void
    {
        $this->authorizationCheckerInterface = $this->createMock(AuthorizationCheckerInterface::class);
        $this->menuItems = [];
    }

    /**
     * @return MockObject|SimpleMenuAdaptor
     */
    public function setUpSimpleMenuAdaptorMock()
    {
        $simpleMenuAdaptorMock = $this->getMockBuilder(SimpleMenuAdaptor::class)
            ->setConstructorArgs([$this->authorizationCheckerInterface, $this->menuItems])
            ->onlyMethods([])
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
        $this->assertSame('menu_item', $children[0]->getLabel());
    }

    public function testHasInterface()
    {
        $simpleMenuAdaptorMock = $this->setUpSimpleMenuAdaptorMock();
        $this->assertInstanceOf(MenuAdaptorInterface::class, $simpleMenuAdaptorMock);
    }

    public function provider(): \Iterator
    {
        /** @var TopMenuItem $parent */
        $parent = $this->createMock(TopMenuItem::class);
        $parent
            ->method('getRoute')
            ->willReturn('test_route')
        ;
        yield 'with no parent' => [null, null];
        yield 'with parent' => [$parent, 'test_route'];
    }
}
