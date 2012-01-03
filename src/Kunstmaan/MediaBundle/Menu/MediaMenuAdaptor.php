<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Kunstmaan\MediaBundle\Menu;

use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\ItemInterface as KnpMenu;

class MediaMenuAdaptor implements \Kunstmaan\AdminBundle\Menu\MenuAdaptorInterface
{
    public function adaptMenu(KnpMenu $menu, Translator $translator)
    {
        $menu->addChild($translator->trans('media.menu.media'), array('route' => 'KunstmaanMediaBundle_folder_show', 'routeParameters' => array('id' => '1', 'slug' => 'media')));
        //$menu[$translator->trans('media.menu.media')]->setUri('#');
        //$menu[$translator->trans('media.menu.media')]->setLinkAttribute('class', 'dropdown-toggle');
        //$menu[$translator->trans('media.menu.media')]->setAttribute('class', 'dropdown');
        //$menu[$translator->trans('media.menu.media')]->setChildrenAttribute('class', 'dropdown-menu');

        $menu[$translator->trans('media.menu.media')]->moveToPosition(1);

           /*$menu[$translator->trans('media.menu.media')]->addChild($translator->trans('media.menu.images'), array('route' => 'KunstmaanMediaBundle_media_images'));
            $menu[$translator->trans('media.menu.media')]->addChild($translator->trans('media.menu.videos'), array('route' => 'KunstmaanMediaBundle_media_videos'));
            $menu[$translator->trans('media.menu.media')]->addChild($translator->trans('media.menu.slides'), array('route' => 'KunstmaanMediaBundle_media_slides'));
            $menu[$translator->trans('media.menu.media')]->addChild($translator->trans('media.menu.files'), array('route' => 'KunstmaanMediaBundle_media_files'));
            $menu[$translator->trans('media.menu.media')]->addChild($translator->trans('media.menu.folders'), array('route' => 'KunstmaanMediaBundle_media_folders'));*/
    }

}