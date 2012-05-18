<?php

namespace Kunstmaan\MediaBundle\Helper\Menu;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\Translation\Translator;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;


/**
 * The Media Menu Adaptor
 */
class MediaMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * 
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

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

        $media_routes = array(
            'Show media' => 'KunstmaanMediaBundle_media_show',
            'Edit metadata' => 'KunstmaanMediaBundle_metadata_edit',
            'Edit slide' => 'KunstmaanMediaBundle_slide_edit',
            'Edit video' => 'KunstmaanMediaBundle_video_edit'
        );

        $create_routes = array(
            'Create slide' => 'KunstmaanMediaBundle_folder_slidecreate',
            'Create video' => 'KunstmaanMediaBundle_folder_videocreate',
            'Create image' => 'KunstmaanMediaBundle_folder_imagecreate',
            'Create file' => 'KunstmaanMediaBundle_folder_filecreate'
        );

        $all_routes = array_merge($create_routes, $media_routes);

        if (is_null($parent)) {
            $galleries = $this->em->getRepository('KunstmaanMediaBundle:Folder')->getAllFolders();
            $currentId = $request->get('id');

            if (isset($currentId)) {
                $currentGallery = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneById($currentId);
            } else if (in_array($request->attributes->get('_route'), $media_routes)) {
                $media     = $this->em->getRepository('KunstmaanMediaBundle:Media')->getMedia($request->get('media_id'), $this->em);
                $currentGallery = $media->getGallery();
            } else if (in_array($request->attributes->get('_route'), $create_routes)) {
                $currentId = $request->get('gallery_id');
                if(isset($currentId)) {
                    $currentGallery = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneById($currentId);
                }
            }

            if(!is_null($currentGallery)) {
                $parents = $currentGallery->getParents();
            } else {
                $parents = array();
            }

            foreach ($galleries as $gallery) {
                $menuitem = new TopMenuItem($menu);
                $menuitem->setRoute('KunstmaanMediaBundle_folder_show');
                $menuitem->setRouteparams(array('id' => $gallery->getId(), 'slug' => $gallery->getSlug()));
                $menuitem->setInternalname($gallery->getName());
                $menuitem->setParent($parent);
                $menuitem->setRole($gallery->getRel());
                if (isset($currentGallery) && (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0 || in_array($request->attributes->get('_route'), $all_routes))) {
                    if ($currentGallery->getId() == $gallery->getId()) {
                        $menuitem->setActive(true);
                    } else {
                        foreach ($parents as $parent) {
                            if ($parent->getId() == $gallery->getId()) {
                                $menuitem->setActive(true);
                                break;
                            }
                        }
                    }
                }
                $children[] = $menuitem;
            }
        } else if ('KunstmaanMediaBundle_folder_show' == $parent->getRoute()) {
            $parentRouteParams = $parent->getRouteparams();
            $parentgallery = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneById($parentRouteParams['id']);
            $galleries = $parentgallery->getChildren();
            $currentId = $request->get('id');

            if (isset($currentId)) {
                $currentGallery = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneById($currentId);
            } else if (in_array($request->attributes->get('_route'), $media_routes)) {
                $media     = $this->em->getRepository('KunstmaanMediaBundle:Media')->getMedia($request->get('media_id'), $this->em);
                $currentGallery = $media->getGallery();
            } else if (in_array($request->attributes->get('_route'), $create_routes)) {
                $currentId = $request->get('gallery_id');
                if(isset($currentId)) {
                    $currentGallery = $this->em->getRepository('KunstmaanMediaBundle:Folder')->findOneById($currentId);
                }
            }

            if (!is_null($currentGallery)) {
                $parentGalleries = $currentGallery->getParents();
            } else {
                $parentGalleries = array();
            }

            foreach ($galleries as $gallery) {
                $menuitem = new MenuItem($menu);
                $menuitem->setRoute('KunstmaanMediaBundle_folder_show');
                $menuitem->setRouteparams(array('id' => $gallery->getId(), 'slug' => $gallery->getSlug()));
                $menuitem->setInternalname($gallery->getName());
                $menuitem->setParent($parent);
                $menuitem->setRole($gallery->getRel());
                if (isset($currentGallery) && (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0 || in_array($request->attributes->get('_route'), $all_routes))) {
                    if ($currentGallery->getId() == $gallery->getId()) {
                        $menuitem->setActive(true);
                    } else {
                        foreach ($parentGalleries as $parentGallery) {
                            if ($parentGallery->getId() == $gallery->getId()) {
                                $menuitem->setActive(true);
                                break;
                            }
                        }
                    }
                }
                $children[] = $menuitem;
            }

            foreach($all_routes as $name => $route) {
                $menuitem = new MenuItem($menu);
                $menuitem->setRoute($route);
                $menuitem->setInternalname($name);
                $menuitem->setParent($parent);
                $menuitem->setAppearInNavigation(false);
                if (stripos($request->attributes->get('_route'), $menuitem->getRoute()) === 0) {
                    $menuitem->setActive(true);
                }

                $children[] = $menuitem;
            }

        }

    }
}