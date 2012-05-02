<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;

class SlideGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'SlideGallery';
    }

    public function getType()
    {
        return 'slide';
    }

    public function getNewGallery(EntityManager $em)
    {
        return new \Kunstmaan\MediaBundle\Entity\SlideGallery($em);
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\SlideGallery';
    }

    function getFormType()
    {
       return new \Kunstmaan\MediaBundle\Form\SlideType();
    }

    function getFormHelper()
    {
        return new \Kunstmaan\MediaBundle\Entity\Slide();
    }

    function getListConfigurator(){
        return new \Kunstmaan\MediaBundle\Helper\MediaList\SlideListConfigurator();
    }
}

?>