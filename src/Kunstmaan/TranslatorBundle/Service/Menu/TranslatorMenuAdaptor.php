<?php

namespace Kunstmaan\TranslatorBundle\Service\Menu;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

class TranslatorMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * Is the bundle enabled?
     * @var boolean
     */
    private $translatorBundleEnabled;

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu      The MenuBuilder
     * @param MenuItem[]  &$children The current children
     * @param MenuItem    $parent    The parent Menu item
     * @param Request     $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (is_null($parent)) {
            return;
        }

        if ('KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            $menuItem = new MenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanTranslatorBundle_settings_translations')
                ->setLabel('Translations')
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

    public function setTranslatorBundleEnabled($translatorBundleEnabled)
    {
        $this->translatorBundleEnabled = $translatorBundleEnabled;
    }
}
