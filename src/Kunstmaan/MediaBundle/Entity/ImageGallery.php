<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Helper\ImageGalleryStrategy;
use Doctrine\ORM\Mapping as ORM;

/**
 * ImageGallery
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_image_galleries")
 * @ORM\HasLifecycleCallbacks
 */
class ImageGallery extends Folder
{

    /**
     * @return ImageGalleryStrategy
     */
    public function getStrategy()
    {
        return new ImageGalleryStrategy();
    }
}