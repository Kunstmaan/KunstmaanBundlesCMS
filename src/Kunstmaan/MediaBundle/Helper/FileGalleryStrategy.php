<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Helper\MediaList\FileListConfigurator;
use Kunstmaan\MediaBundle\Form\MediaType;
use Kunstmaan\MediaBundle\Entity\FileGallery;

class FileGalleryStrategy implements GalleryStrategyInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'FileGallery';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'file';
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return \Kunstmaan\MediaBundle\Entity\FileGallery
     */
    public function getNewGallery(EntityManager $em)
    {
        return new FileGallery($em);
    }

    /**
     * @return string
     */
    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\FileGallery';
    }

    /**
     * @return \Kunstmaan\MediaBundle\Form\MediaType
     */
    function getFormType()
    {
        return new MediaType();
    }

    /**
     * @return MediaHelper
     */
    function getFormHelper()
    {
        return new MediaHelper();
    }

    /**
     * @return MediaList\FileListConfigurator
     */
    function getListConfigurator()
    {
        return new FileListConfigurator();
    }
}

?>