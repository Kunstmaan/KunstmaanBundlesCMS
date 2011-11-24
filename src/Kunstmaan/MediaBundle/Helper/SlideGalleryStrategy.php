<?php

namespace Kunstmaan\KMediaBundle\Helper;

/**
 * Comment controller.
 */
class SlideGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'SlideGallery';
    }

    public function getType()
    {
        return 'slide';
    }

    public function getNewGallery()
    {
        return new \Kunstmaan\KMediaBundle\Entity\SlideGallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\KMediaBundle\Entity\SlideGallery';
    }
}

?>