<?php

namespace Kunstmaan\KMediaBundle\Helper;

/**
 * Comment controller.
 */
class ImageGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'ImageGallery';
    }

    public function getType()
    {
        return 'image';
    }

    public function getNewGallery()
    {
        return new \Kunstmaan\KMediaBundle\Entity\ImageGallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\KMediaBundle\Entity\ImageGallery';
    }
}

?>