<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;

class VideoGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'VideoGallery';
    }

    public function getType()
    {
        return 'video';
    }

    public function getNewGallery(EntityManager $em)
    {
        return new \Kunstmaan\MediaBundle\Entity\VideoGallery($em);
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

    function getListConfigurator(){
        return new \Kunstmaan\MediaBundle\Helper\MediaList\VideoListConfigurator();
    }
}

?>