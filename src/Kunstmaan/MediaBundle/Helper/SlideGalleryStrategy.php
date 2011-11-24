<?php

namespace Kunstmaan\MediaBundle\Helper;

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
        return new \Kunstmaan\MediaBundle\Entity\SlideGallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\SlideGallery';
    }
}

?>