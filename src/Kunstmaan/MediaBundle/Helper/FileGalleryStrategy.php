<?php

namespace Kunstmaan\KMediaBundle\Helper;

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
        return new \Kunstmaan\KMediaBundle\Entity\FileGallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\KMediaBundle\Entity\FileGallery';
    }
}

?>