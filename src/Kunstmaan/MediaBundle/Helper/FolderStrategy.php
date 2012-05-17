<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Helper\MediaList\MediaListConfigurator;

class FolderStrategy implements GalleryStrategyInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Folder';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'folder';
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return \Kunstmaan\MediaBundle\Entity\Folder
     */
    public function getNewGallery(EntityManager $em)
    {
        return new Folder($em);
    }

    /**
     * @return string
     */
    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\Folder';
    }

    /**
     * @return null
     */
    function getFormType()
    {
        return NULL;
    }

    /**
     * @return null
     */
    function getFormHelper()
    {
        return NULL;
    }

    /**
     * @return MediaList\MediaListConfigurator
     */
    function getListConfigurator()
    {
        return new MediaListConfigurator();
    }
}

?>