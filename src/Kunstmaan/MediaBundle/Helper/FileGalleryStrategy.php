<?php

namespace Kunstmaan\MediaBundle\Helper;

/**
 * Comment controller.
 */
class FileGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'FileGallery';
    }

    public function getType()
    {
        return 'file';
    }

    public function getNewGallery()
    {
        return new \Kunstmaan\MediaBundle\Entity\FileGallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\FileGallery';
    }

    function getFormType()
    {
        return new \Kunstmaan\MediaBundle\Form\MediaType();
    }

    function getFormHelper()
    {
        return new MediaHelper();
    }
}

?>