<?php

namespace Kunstmaan\TranslatorBundle\Service\Menu;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

class TranslatorMenuAdaptor implements MenuAdaptorInterface
{

    /**
     * @var \Kunstmaan\TranslatorBundle\Service\TranslationManager
     */
    private $translationManager;

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
        // Build the top menu when the parent is null
        if (is_null($parent) && $this->translatorBundleEnabled === true) {
            $children[] = $this->getTopMenuItem($menu, $request);
        }
    }

    /**
     * Build a top menu item
     * @param  MenuBuilder $menu
     * @param  Request     $request
     * @return TopMenuItem
     */
    public function getTopMenuItem(MenuBuilder $menu, Request $request = null)
    {
        $menuitem = new TopMenuItem($menu);
        $menuitem->setRoute('KunstmaanTranslatorBundle_translations_show');
        $menuitem->setRouteparams(array('domain' => $this->translationManager->getFirstDefaultDomainName()));
        $menuitem->setInternalname('Translations');
        $menuitem->setParent(null);

        return $menuitem;
    }

    public function setTranslationManager($translationManager)
    {
        $this->translationManager = $translationManager;
    }

    public function setTranslatorBundleEnabled($translatorBundleEnabled)
    {
        $this->translatorBundleEnabled = $translatorBundleEnabled;
    }
}
