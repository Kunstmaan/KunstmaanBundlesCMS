<?php

namespace {{ namespace }}\Helper\Menu;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

class {{ entity_class }}MenuAdaptor implements MenuAdaptorInterface
{

    private $overviewpageIds = array();

    /**
     * @param EntityManager $em The entity manager
     */
    public function __construct(EntityManager $em)
    {
        $overviewpageNodes = $em->getRepository('KunstmaanNodeBundle:Node')->findByRefEntityName('{{ namespace|replace({"\\": "\\\\"}) }}\\Entity\\{{ entity_class }}\\{{ entity_class }}OverviewPage');
        foreach ($overviewpageNodes as $overviewpageNode) {
            $this->overviewpageIds[] = $overviewpageNode->getId();
        }
    }

    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            // Page
            $menuitem = new TopMenuItem($menu);
            $menuitem->setRoute('{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_{{ entity_class|lower }}page');
            $menuitem->setInternalName('{{ entity_class }}');
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuitem;
            // Author
            $menuitem = new TopMenuItem($menu);
            $menuitem->setRoute('{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_{{ entity_class|lower }}author');
            $menuitem->setInternalName('{{ entity_class }} Authors');
            $menuitem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                $menuitem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuitem;
        }

        //don't load children
        if (!is_null($parent) && 'KunstmaanNodeBundle_nodes_edit' == $parent->getRoute()) {
            foreach ($children as $key => $child) {
                if ('KunstmaanNodeBundle_nodes_edit' == $child->getRoute()){
                    $params = $child->getRouteParams();
                    $id = $params['id'];
                    if (in_array($id, $this->overviewpageIds)) {
                        $child->setChildren(array());
                    }
                }
            }
        }
    }
}
