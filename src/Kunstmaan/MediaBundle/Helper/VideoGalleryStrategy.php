<?php

namespace Kunstmaan\MediaBundle\Helper;

/**
 * Comment controller.
 */
class VideoGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'VideoGallery';
    }

    public function getType()
    {
        return 'video';
    }

    public function getNewGallery()
    {
        return new \Kunstmaan\MediaBundle\Entity\VideoGallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\VideoGallery';
    }

    function getFormType()
    {
        return new \Kunstmaan\MediaBundle\Form\VideoType();
    }

    function getFormHelper()
    {
        return new \Kunstmaan\MediaBundle\Entity\Video();
    }
}

?>