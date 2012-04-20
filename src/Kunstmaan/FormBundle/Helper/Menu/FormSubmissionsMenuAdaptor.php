<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\FormBundle\Helper\Menu;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;

class FormSubmissionsMenuAdaptor implements MenuAdaptorInterface
{

    public function getChildren(MenuBuilder $menu, MenuItem $parent = null, Request $request)
    {
        if(is_null($parent)) {
            $menuitem = new TopMenuItem($menu);
            $menuitem->setRoute('KunstmaanFormBundle_formsubmissions');
            $menuitem->setInternalname('Form submissions');
            $menuitem->setParent($parent);
            if(stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0){
                $menuitem->setActive(true);
            }
            return array($menuitem);
        }
        return null;
    }

}
