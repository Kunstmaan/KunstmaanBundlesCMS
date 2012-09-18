<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Helper\FileGalleryStrategy;
use Doctrine\ORM\Mapping as ORM;

/**
 * FileGallery
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_media_file_galleries")
 * @ORM\HasLifecycleCallbacks
 */
class FileGallery extends Folder
{

    /**
     * @return FileGalleryStrategy
     */
    public function getStrategy()
    {
        return new FileGalleryStrategy();
    }
}