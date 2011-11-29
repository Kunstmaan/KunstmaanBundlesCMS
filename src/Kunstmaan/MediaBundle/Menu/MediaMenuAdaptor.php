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
        $menu->addChild('Media', array('route' => 'KunstmaanMediaBundle_media'));
        $menu['Media']->setUri('#');
        $menu['Media']->setLinkAttribute('class', 'dropdown-toggle');
        $menu['Media']->setAttribute('class', 'dropdown');
        $menu['Media']->setChildrenAttribute('class', 'dropdown-menu');

        $menu['Media']->moveToPosition(1);

            $menu['Media']->addChild('Images', array('route' => 'KunstmaanMediaBundle_media_images'));
            $menu['Media']->addChild('Videos', array('route' => 'KunstmaanMediaBundle_media_videos'));
            $menu['Media']->addChild('Slides', array('route' => 'KunstmaanMediaBundle_media_slides'));
            $menu['Media']->addChild('Files', array('route' => 'KunstmaanMediaBundle_media_files'));
    }

}