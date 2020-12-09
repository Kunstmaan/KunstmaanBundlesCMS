<?php

namespace Kunstmaan\TranslatorBundle\Service\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;

class TranslatorMenuAdaptor implements MenuAdaptorInterface
{
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (\is_null($parent)) {
            return;
        }

        if ('KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            $menuItem = new MenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanTranslatorBundle_settings_translations')
                ->setLabel('translator.translator.title')
                ->setUniqueId('Translations')
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        } elseif ('KunstmaanTranslatorBundle_settings_translations' == $parent->getRoute()) {
            $menuItem = new MenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanTranslatorBundle_settings_translations_add')
                ->setUniqueId('Add translation')
                ->setLabel('Add translation')
                ->setParent($parent)
                ->setAppearInNavigation(false);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;

            $menuItem = new MenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanTranslatorBundle_settings_translations_edit')
                ->setUniqueId('Edit translation')
                ->setLabel('Edit translation')
                ->setParent($parent)
                ->setAppearInNavigation(false);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}
