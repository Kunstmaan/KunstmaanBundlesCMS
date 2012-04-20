<?php
namespace Kunstmaan\SearchBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;

class SearchMenuAdaptor implements MenuAdaptorInterface
{
public function getChildren(MenuBuilder $menu, MenuItem $parent = null, Request $request)
    {
        if (is_null($parent)) {
            
        } else if ('KunstmaanAdminBundle_settings' == $parent->getRoute()){
            $menuitem = new MenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminBundle_settings_searches');
            $menuitem->setInternalname('Searches');
            $menuitem->setRouteparams(array());
            $menuitem->setParent($parent);
            if(stripos($request->attributes->get('_route'), "KunstmaanAdminBundle_settings_searches") === 0){
                $menuitem->setActive(true);
            }
            $result[] = $menuitem;
        }
    }
    
}