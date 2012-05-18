<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;
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
    function getListConfigurator()
    {
        return NULL;
    }
}

?>