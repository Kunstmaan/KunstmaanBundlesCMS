<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Helper\SlideGalleryStrategy;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @ORM\Entity
 * @ORM\Table(name="media_gallery_slide")
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