<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Folder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Form\MediaType;

/**
 * ImageGalleryStrategy
 */
class ImageGalleryStrategy implements GalleryStrategyInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'ImageGallery';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'image';
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getNewBulkUploadMediaInstance()
    {
        return new Image();
    }

    /**
     * @return string
     */
    public function getBulkUploadAccept()
    {
        return 'image/*';
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\ImageGallery
     */
    public function getNewGallery()
    {
        return new ImageGallery();
    }

    /**
     * @return string
     */
    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\ImageGallery';
    }

    /**
     * @return MediaType
     */
    public function getFormType()
    {
        return new MediaType();
    }

    /**
     * @return MediaHelper
     */
    public function getFormHelper()
    {
        return new MediaHelper();
    }

    /**
     * @param Folder $folder
     *
     * @return null
     */
    public function getListConfigurator(Folder $folder)
    {
        return null;
    }
}