<?php

namespace Kunstmaan\MediaBundle\Helper;

/**
 * Comment controller.
 */
class FolderStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'Folder';
    }

    public function getType()
    {
        return 'folder';
    }

    public function getNewGallery()
    {
        return new \Kunstmaan\MediaBundle\Entity\Folder();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\Folder';
    }

    function getFormType()
    {
        return null;
    }

    function getFormHelper()
    {
        return null;
    }

    function getListConfigurator(){
        return new \Kunstmaan\MediaBundle\Helper\MediaList\MediaListConfigurator();
    }
}

?>