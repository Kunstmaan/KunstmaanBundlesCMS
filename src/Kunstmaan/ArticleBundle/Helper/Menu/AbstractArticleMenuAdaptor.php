<?php

namespace Kunstmaan\ArticleBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

class AbstractArticleMenuAdaptor implements MenuAdaptorInterface
{
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        /*
        // page
        if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuitem = new TopMenuItem($menu);
            $menuitem->setRoute('KunstmaanArticleBundle_admin_abstractarticlepage');
            $menuitem->setInternalName('Articles');
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuitem;
        }
        // author
        if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuitem = new TopMenuItem($menu);
            $menuitem->setRoute('KunstmaanArticleBundle_admin_abstractarticleauthor');
            $menuitem->setInternalName('Article Authors');
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuitem;
        }
        */
    }
}
