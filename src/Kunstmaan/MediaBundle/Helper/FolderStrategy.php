<?php

namespace Kunstmaan\MediaBundle\Helper;

/**
 * Comment controller.
 */
use Doctrine\ORM\EntityManager;

class FolderStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'Folder';
    }

    public function getType()
    {
        return 'folder';
    }

    public function getNewGallery(EntityManager $em)
    {
        return new \Kunstmaan\MediaBundle\Entity\Folder($em);
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\Folder';
    }

    function getFormType()
    {
        return null;
    }

    function getFormHelper()
    {
        return null;
    }

    function getListConfigurator(){
        return new \Kunstmaan\MediaBundle\Helper\MediaList\MediaListConfigurator();
    }
}

?>