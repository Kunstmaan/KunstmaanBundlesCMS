<?php

namespace Kunstmaan\MediaBundle\Helper;

/**
 * Comment controller.
 */
use Doctrine\ORM\EntityManager;

class FileGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'FileGallery';
    }

    public function getType()
    {
        return 'file';
    }

    public function getNewGallery(EntityManager $em)
    {
        return new \Kunstmaan\MediaBundle\Entity\FileGallery($em);
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

    function getListConfigurator(){
        return new \Kunstmaan\MediaBundle\Helper\MediaList\FileListConfigurator();
    }
}

?>