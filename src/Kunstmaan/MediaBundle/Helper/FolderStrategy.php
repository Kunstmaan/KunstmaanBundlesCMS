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
        return new \Kunstmaan\MediaBundle\Entity\Gallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\Gallery';
    }

    function getFormType()
    {
        return new \Kunstmaan\MediaBundle\Form\MediaType();
    }

    function getFormHelper()
    {
        return new MediaHelper();
    }

    function getListConfigurator(){
        return new \Kunstmaan\MediaBundle\Helper\MediaList\FileListConfigurator();
    }
}

?>