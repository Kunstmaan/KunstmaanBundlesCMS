<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;

class ImageGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'ImageGallery';
    }

    public function getType()
    {
        return 'image';
    }

    public function getNewGallery(EntityManager $em)
    {
        return new \Kunstmaan\MediaBundle\Entity\ImageGallery($em);
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\ImageGallery';
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
        return null;
    }
}

?>