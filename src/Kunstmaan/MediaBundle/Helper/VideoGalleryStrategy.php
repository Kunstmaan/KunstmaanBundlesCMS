<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Helper\MediaList\VideoListConfigurator;
use Kunstmaan\MediaBundle\Entity\Video;

class VideoGalleryStrategy implements GalleryStrategyInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'VideoGallery';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'video';
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
     * @return \Kunstmaan\MediaBundle\Entity\VideoGallery
     */
    public function getNewGallery(EntityManager $em)
    {
        return new VideoGallery($em);
    }

    /**
     * @return string
     */
    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\VideoGallery';
    }

    /**
     * @return \Kunstmaan\MediaBundle\Form\VideoType
     */
    function getFormType()
    {
        return new VideoType();
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Video
     */
    function getFormHelper()
    {
        return new Video();
    }

    /**
     * @return MediaList\VideoListConfigurator
     */
    function getListConfigurator($folder)
    {
        return new VideoListConfigurator($folder);
    }
}

?>