<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Helper\SlideGalleryStrategy;

/**
 * SlideGallery
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_media_slide_galleries")
 * @ORM\HasLifecycleCallbacks
 */
class SlideGallery extends Folder
{

    /**
     * @return SlideGalleryStrategy
     */
    public function getStrategy()
    {
        return new SlideGalleryStrategy();
    }
}