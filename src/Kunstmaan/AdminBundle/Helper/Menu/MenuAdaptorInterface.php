<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\Translation\Translator;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;
use Symfony\Component\HttpFoundation\Request;

interface MenuAdaptorInterface
{
    function getChildren(MenuBuilder $menu, MenuItem $parent = null, Request $request);
}