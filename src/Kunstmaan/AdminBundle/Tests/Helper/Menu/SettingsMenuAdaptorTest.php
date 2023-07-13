<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use PHPUnit\Framework\MockObject\MockObject;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\SettingsMenuAdaptor;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SettingsMenuAdaptorTest extends TestCase
{
    /** @var AuthorizationCheckerInterface (mock) */
    private $authorizationCheckerInterface;

    private bool $isEnabledVersionChecker = true;

    public function setUp(): void
    {
        $this->authorizationCheckerInterface = $this->createMock(AuthorizationCheckerInterface::class);
    }

    /**
     * @dataProvider provider
     */
    public function testAdaptChildren(?TopMenuItem $parent, string $requestRoute, int $expectedCount, ?string $expectedLabel, bool $granted)
    {
        $children = [];
        /** @var MenuBuilder $menuBuilderMock */
        $menuBuilderMock = $this->createMock(MenuBuilder::class);
        /** @var Request $request */
        $request = new Request([], [], ['_route' => $requestRoute]);
        $this->authorizationCheckerInterface
            ->method('isGranted')
            ->willReturn($granted);

        $settingsMenuAdaptorMock = $this->setUpSettingsMenuAdaptorMock();
        $settingsMenuAdaptorMock->adaptChildren($menuBuilderMock, $children, $parent, $request);

        $this->assertCount($expectedCount, $children);
        $this->assertContainsOnlyInstancesOf(MenuItem::class, $children);

        if (null !== $expectedLabel) {
            $this->assertSame($expectedLabel, $children[0]->getLabel());
        }
    }

    /**
     * @return MockObject|SettingsMenuAdaptor
     */
    public function setUpSettingsMenuAdaptorMock()
    {
        $simpleMenuAdaptorMock = $this->getMockBuilder(SettingsMenuAdaptor::class)
            ->setConstructorArgs([$this->authorizationCheckerInterface, $this->isEnabledVersionChecker])
            ->onlyMethods([])
            ->getMock()
        ;

        return $simpleMenuAdaptorMock;
    }

    public function testHasInterface()
    {
        $settingsMenuAdaptorMock = $this->setUpSettingsMenuAdaptorMock();
        $this->assertInstanceOf(MenuAdaptorInterface::class, $settingsMenuAdaptorMock);
    }

    public function provider(): \Iterator
    {
        /** @var TopMenuItem $parent */
        $parent = $this->createMock(TopMenuItem::class);
        $parent
            ->method('getRoute')
            ->willReturn('KunstmaanAdminBundle_settings')
        ;

        /** @var TopMenuItem $parentWithOtherRoute */
        $parentWithOtherRoute = $this->createMock(TopMenuItem::class);
        $parentWithOtherRoute
            ->method('getRoute')
            ->willReturn('KunstmaanAdminBundle_other')
        ;
        yield 'with no parent and no route' => [null, 'KunstmaanAdminBundle_settings', 1, 'settings.title', true];
        yield 'with parent and route is settings but not granted' => [$parent, 'kunstmaanadminbundle_admin_exception', 1, 'settings.exceptions.title', false];
        yield 'with parent and route is settings and first active' => [$parent, 'KunstmaanAdminBundle_settings_bundle_version', 2, 'settings.version.bundle', true];
        yield 'with parent and route is settings and second active' => [$parent, 'kunstmaanadminbundle_admin_exception', 2, 'settings.version.bundle', true];
        yield 'with parent and route is not settings' => [$parentWithOtherRoute, '', 0, null, true];
    }
}
