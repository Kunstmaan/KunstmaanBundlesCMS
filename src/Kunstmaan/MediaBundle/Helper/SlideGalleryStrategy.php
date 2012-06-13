<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Helper\MediaList\SlideListConfigurator;
use Kunstmaan\MediaBundle\Entity\Slide;
use Kunstmaan\MediaBundle\Form\SlideType;

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
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getNewBulkUploadMediaInstance() {
        return null;
    }

    public function getBulkUploadAccept() {
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
     * @return \Kunstmaan\MediaBundle\Form\SlideType
     */
    function getFormType()
    {
        return new SlideType();
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Slide
     */
    function getFormHelper()
    {
        return new Slide();
    }

    /**
     * @return MediaList\SlideListConfigurator
     */
    function getListConfigurator($folder)
    {
        return new SlideListConfigurator($folder);
    }
}

?>