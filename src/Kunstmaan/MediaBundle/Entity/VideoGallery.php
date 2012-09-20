<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Helper\VideoGalleryStrategy;

/**
 * VideoGallery
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_video_galleries")
 * @ORM\HasLifecycleCallbacks
 */
class VideoGallery extends Folder
{

    /**
     * @return VideoGalleryStrategy
     */
    public function getStrategy()
    {
        return new VideoGalleryStrategy();
    }
}