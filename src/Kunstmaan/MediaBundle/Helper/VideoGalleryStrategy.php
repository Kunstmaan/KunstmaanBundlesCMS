<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\Folder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\AdminList\VideoListConfigurator;
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
     * @return \Kunstmaan\MediaBundle\Entity\VideoGallery
     */
    public function getNewGallery()
    {
        return new VideoGallery();
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
    public function getListConfigurator(Folder $folder)
    {
        return new VideoListConfigurator($folder);
    }
}