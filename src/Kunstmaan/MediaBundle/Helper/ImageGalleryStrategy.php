<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\Image;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Form\MediaType;

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
    public function getNewBulkUploadMediaInstance() {
        return new Image();
    }

    public function getBulkUploadAccept() {
        return 'image/*';
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return \Kunstmaan\MediaBundle\Entity\ImageGallery
     */
    public function getNewGallery(EntityManager $em)
    {
        return new ImageGallery($em);
    }

    /**
     * @return string
     */
    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\ImageGallery';
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
     * @return null
     */
    function getListConfigurator($folder)
    {
        return NULL;
    }
}

?>