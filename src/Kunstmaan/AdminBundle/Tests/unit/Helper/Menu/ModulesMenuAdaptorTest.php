<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\ModulesMenuAdaptor;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ModulesMenuAdaptorTest extends TestCase
{
    public function testAdaptChildren()
    {
        $children = [];

        /** @var MenuBuilder $menuBuilderMock */
        $menuBuilderMock = $this->createMock(MenuBuilder::class);

        /** @var Request $request */
        $request = new Request([], [], ['_route' => 'KunstmaanAdminBundle_modules']);

        $moduleMenuAdaptor = new ModulesMenuAdaptor();
        $moduleMenuAdaptor->adaptChildren($menuBuilderMock, $children, null, $request);

        $this->assertContainsOnlyInstancesOf(TopMenuItem::class, $children);
    }

    public function testHasInterface()
    {
        $moduleMenuAdaptor = new ModulesMenuAdaptor();
        $this->assertInstanceOf(MenuAdaptorInterface::class, $moduleMenuAdaptor);
    }
}
