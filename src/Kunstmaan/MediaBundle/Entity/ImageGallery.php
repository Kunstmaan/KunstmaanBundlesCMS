<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Helper\ImageGalleryStrategy;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @ORM\Entity
 * @ORM\Table(name="media_gallery_image")
 * @ORM\HasLifecycleCallbacks
 */
class ImageGallery extends Folder
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function getStrategy()
    {
        return new ImageGalleryStrategy();
    }
}