<?php
namespace Kunstmaan\MediaBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;

use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;

class MediaMenuAdaptor implements MenuAdaptorInterface
{
    private $em;
    public function __construct($em){
        $this->em = $em;
    }
    
    public function getChildren(MenuBuilder $menu, MenuItem $parent = null, Request $request)
    {
        $result = array();
        if(is_null($parent)) {
            $galleries = $this->em->getRepository('KunstmaanMediaBundle:Folder')->getAllFolders();
            foreach( $galleries as $gallery) {
                $menuitem = new TopMenuItem($menu);
                $menuitem->setRoute('KunstmaanMediaBundle_folder_show');
                $menuitem->setRouteparams(array('id' => $gallery->getId(), 'slug' => $gallery->getSlug()));
                $menuitem->setInternalname($gallery->getName());
                $menuitem->setParent($parent);
                $menuitem->setRole($gallery->getRel());
                if(stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0){
                    $currentGallery = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneById($request->get('id'));
                    if($currentGallery->getId() == $gallery->getId()){
                        $menuitem->setActive(true);
                    } else {
                        $parents = $currentGallery->getParents();
                        foreach($parents as $parent){
                            if($parent->getId() == $gallery->getId()){
                                $menuitem->setActive(true);
                                break;
                            }
                        }
                    }
                }
                $result[] = $menuitem;
            }
        } else if ('KunstmaanMediaBundle_folder_show' == $parent->getRoute()){
            $parentRouteParams = $parent->getRouteparams();
            $parentgallery = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneById($parentRouteParams['id']);
            $galleries = $parentgallery->getChildren();
            foreach( $galleries as $gallery) {
                $menuitem = new MenuItem($menu);
                $menuitem->setRoute('KunstmaanMediaBundle_folder_show');
                $menuitem->setRouteparams(array('id' => $gallery->getId(), 'slug' => $gallery->getSlug()));
                $menuitem->setInternalname($gallery->getName());
                $menuitem->setParent($parent);
                $menuitem->setRole($gallery->getRel());
                if(stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0){
                    $currentGallery = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneById($request->get('id'));
                    if($currentGallery->getId() == $gallery->getId()){
                        $menuitem->setActive(true);
                    } else {
                        $parentGalleries = $currentGallery->getParents();
                        foreach($parentGalleries as $parentGallery){
                            if($parentGallery->getId() == $gallery->getId()){
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