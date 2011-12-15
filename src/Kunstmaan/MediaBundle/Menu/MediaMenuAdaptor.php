<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\MediaBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;

class MediaMenuAdaptor implements \Kunstmaan\AdminBundle\Menu\MenuAdaptorInterface
{
    public function adaptMenu(KnpMenu $menu)
    {
        $menu->addChild('media.menu.media', array('route' => 'KunstmaanMediaBundle_media'));
        $menu['media.menu.media']->setUri('#');
        $menu['media.menu.media']->setLinkAttribute('class', 'dropdown-toggle');
        $menu['media.menu.media']->setAttribute('class', 'dropdown');
        $menu['media.menu.media']->setChildrenAttribute('class', 'dropdown-menu');

        $menu['media.menu.media']->moveToPosition(1);

            $menu['media.menu.media']->addChild('media.menu.images', array('route' => 'KunstmaanMediaBundle_media_images'));
            $menu['media.menu.media']->addChild('media.menu.videos', array('route' => 'KunstmaanMediaBundle_media_videos'));
            $menu['media.menu.media']->addChild('media.menu.slides', array('route' => 'KunstmaanMediaBundle_media_slides'));
            $menu['media.menu.media']->addChild('media.menu.les', array('route' => 'KunstmaanMediaBundle_media_files'));
    }

}