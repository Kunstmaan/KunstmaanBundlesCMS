<?php

namespace {{ namespace }}\Helper\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

class {{ entity_class }}MenuAdaptor implements MenuAdaptorInterface
{
    private $overviewpageIds = null;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
	    $this->em = $em;
    }

    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (null === $this->overviewpageIds) {
            $overviewPageNodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->findByRefEntityName('{{ namespace|replace({"\\": "\\\\"}) }}\\Entity\\Pages\\{{ entity_class }}OverviewPage');
            $this->overviewpageIds = array();
            foreach ($overviewPageNodes as $overviewPageNode) {
                $this->overviewpageIds[] = $overviewPageNode->getId();
            }
        }

        if (null !== $parent && 'KunstmaanAdminBundle_modules' === $parent->getRoute()) {
            // submenu
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setUniqueId('{{ entity_class|lower }}')
                ->setLabel('{{ entity_class }}')
                ->setParent($parent);
            if (in_array($request->attributes->get('_route'), [
                '{{ bundle.getName()|lower }}_admin_blogitem',
                '{{ bundle.getName()|lower }}_admin_blogsubscription'
            ])) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;

        }

        if (null !== $parent && '{{ entity_class|lower }}' === $parent->getUniqueId()) {
            // Page
            $menuItem = new TopMenuItem($menu);
            $menuItem
		        ->setRoute('{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page')
		        ->setLabel('Pages')
                ->setUniqueId('{{ entity_class }}')
                ->setParent($parent)
            ;
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;

            //%menuAdaptorPartial.php.twig%
        }

        //don't load children
        if (null !== $parent && 'KunstmaanNodeBundle_nodes_edit' === $parent->getRoute()) {
            foreach ($children as $key => $child) {
                if ('KunstmaanNodeBundle_nodes_edit' === $child->getRoute()){
                    $params = $child->getRouteParams();
                    $id = $params['id'];
                    if (in_array($id, $this->overviewpageIds, true)) {
                        $child->setChildren([]);
                    }
                }
            }
        }
    }
}
