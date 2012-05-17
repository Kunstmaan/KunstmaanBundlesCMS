<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManager;

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
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return \Kunstmaan\MediaBundle\Entity\VideoGallery
     */
    public function getNewGallery(EntityManager $em)
    {
        return new \Kunstmaan\MediaBundle\Entity\VideoGallery($em);
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
        return new \Kunstmaan\MediaBundle\Form\VideoType();
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Video
     */
    function getFormHelper()
    {
        return new \Kunstmaan\MediaBundle\Entity\Video();
    }

    /**
     * @return MediaList\VideoListConfigurator
     */
    function getListConfigurator()
    {
        return new \Kunstmaan\MediaBundle\Helper\MediaList\VideoListConfigurator();
    }
}

?>