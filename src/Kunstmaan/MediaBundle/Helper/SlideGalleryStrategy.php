<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Media;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Helper\MediaList\SlideListConfigurator;
use Kunstmaan\MediaBundle\Entity\Slide;
use Kunstmaan\MediaBundle\Form\SlideType;

/**
 * SlideGalleryStrategy
 */
class SlideGalleryStrategy implements GalleryStrategyInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'SlideGallery';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'slide';
    }

    /**
     * @return Media
     */
    public function getNewBulkUploadMediaInstance()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getBulkUploadAccept()
    {
        return null;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return \Kunstmaan\MediaBundle\Entity\SlideGallery
     */
    public function getNewGallery(EntityManager $em)
    {
        return new SlideGallery($em);
    }

    /**
     * @return string
     */
    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\SlideGallery';
    }

    /**
     * @return SlideType
     */
    public function getFormType()
    {
        return new SlideType();
    }

    /**
     * @return Slide
     */
    public function getFormHelper()
    {
        return new Slide();
    }

    /**
     * @param Folder $folder
     *
     * @return SlideListConfigurator
     */
    public function getListConfigurator($folder)
    {
        return new SlideListConfigurator($folder);
    }
}