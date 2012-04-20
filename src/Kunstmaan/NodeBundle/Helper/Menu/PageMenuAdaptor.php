<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\AdminNodeBundle\Helper\Menu;

use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;

use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;

use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;

class PageMenuAdaptor implements MenuAdaptorInterface
{
    private $container;
    private $nodemenu;
    
    public function __construct($container){
        $this->container = $container;
    }
    
    public function getChildren(MenuBuilder $menu, MenuItem $parent = null, Request $request)
    {
        if(is_null($this->nodemenu)){
            $node = null;
            if($request->attributes->get('_route') == 'KunstmaanAdminNodeBundle_pages_edit'){
                $node = $this->container->get("doctrine")->getEntityManager()->getRepository('KunstmaanAdminNodeBundle:Node')->findOneById($request->attributes->get('id'));
            }
            $this->nodemenu = new NodeMenu($this->container, $request->getSession()->getLocale(), $node, 'write', true);
        }
        $result = array();
        if(is_null($parent)) {
            $menuitem = new TopMenuItem($menu);
            $menuitem->setRoute('KunstmaanAdminNodeBundle_pages');
            $menuitem->setInternalname("Pages");
            $menuitem->setParent($parent);
            if(stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0){
                $menuitem->setActive(true);
            }
            $result[] = $menuitem;
        } else if ('KunstmaanAdminNodeBundle_pages' == $parent->getRoute()){
            $topnodes = $this->nodemenu->getTopNodes();
            foreach( $topnodes as $child) {
                $menuitem = new MenuItem($menu);
                $menuitem->setRoute('KunstmaanAdminNodeBundle_pages_edit');
                $menuitem->setRouteparams(array('id' => $child->getId()));
                $menuitem->setInternalname($child->getTitle());
                $menuitem->setParent($parent);
                $menuitem->setRole('page');
                if(stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0){
                    $currentNode = $this->container->get("doctrine")->getEntityManager()->getRepository('KunstmaanAdminNodeBundle:Node')->findOneById($request->get('id'));
                    if($currentNode->getId() == $child->getId()){
                        $menuitem->setActive(true);
                    } else {
                        $parentNodes = $currentNode->getParents();
                        foreach($parentNodes as $parentNode){
                            if($parentNode->getId() == $child->getId()){
                                $menuitem->setActive(true);
                                break;
                            }
                        }
                    }
                }
                $result[] = $menuitem;
            }
        } else if ('KunstmaanAdminNodeBundle_pages_edit' == $parent->getRoute()){
            $parentRouteParams = $parent->getRouteparams();
            $node = $this->container->get("doctrine")->getEntityManager()->getRepository('KunstmaanAdminNodeBundle:Node')->findOneById($parentRouteParams['id']);
            $nodemenu = new NodeMenu($this->container, $request->getSession()->getLocale(), $node, 'write', true);
            $children = $nodemenu->getCurrent()->getChildren();
            foreach( $children as $child) {
                $menuitem = new MenuItem($menu);
                $menuitem->setRoute('KunstmaanAdminNodeBundle_pages_edit');
                $menuitem->setRouteparams(array('id' => $child->getId()));
                $menuitem->setInternalname($child->getTitle());
                $menuitem->setParent($parent);
                $menuitem->setRole('page');
                if(stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0){
                    $currentNode = $this->container->get("doctrine")->getEntityManager()->getRepository('KunstmaanAdminNodeBundle:Node')->findOneById($request->get('id'));
                    if($currentNode->getId() == $child->getId()){
                        $menuitem->setActive(true);
                    } else {
                        $parentNodes = $currentNode->getParents();
                        foreach($parentNodes as $parentNode){
                            if($parentNode->getId() == $child->getId()){
                                $menuitem->setActive(true);
                                break;
                            }
                        }
                    }
                }
                $result[] = $menuitem;
            }
        }
        return $result;
    }
    
}