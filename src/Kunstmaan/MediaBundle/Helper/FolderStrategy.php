<?php

namespace Kunstmaan\MediaBundle\Helper;

use Symfony\Component\Locale\Exception\NotImplementedException;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\AdminList\MediaListConfigurator;

/**
 * FolderStrategy
 */
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
     * @return null
     */
    public function getNewBulkUploadMediaInstance()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getBulkUploadAccept()
    {
        return null;
    }

    /**
     * @return Folder
     */
    public function getNewGallery()
    {
        return new Folder();
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
    public function getFormType()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getFormHelper()
    {
        return null;
    }

    /**
     * @param Folder $folder
     *
     * @return MediaListConfigurator
     */
    public function getListConfigurator(Folder $folder)
    {
        return new MediaListConfigurator($folder);
    }
}

