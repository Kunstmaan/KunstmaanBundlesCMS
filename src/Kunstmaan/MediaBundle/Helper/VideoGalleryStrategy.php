<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\Helper\MediaList\VideoListConfigurator;
use Kunstmaan\MediaBundle\Entity\Video;

/**
 * VideoGalleryStrategy
 */
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
    public function getFormType()
    {
        return new VideoType();
    }

    /**
     * @return Video
     */
    public function getFormHelper()
    {
        return new Video();
    }

    /**
     * @param Folder $folder
     *
     * @return VideoListConfigurator
     */
    public function getListConfigurator($folder)
    {
        return new VideoListConfigurator($folder);
    }
}