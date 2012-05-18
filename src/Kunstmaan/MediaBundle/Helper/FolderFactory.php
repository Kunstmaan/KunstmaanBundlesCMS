<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Entity\Folder;

class FolderFactory
{

    /**
     * @static
     * @return array
     */
    private static function getTypeArray()
    {
        return array(
            "folder" => new Folder(),
            "file"   => new FileGallery(),
            "image"  => new ImageGallery(),
            "slide"  => new SlideGallery(),
            "video"  => new VideoGallery()
        );
    }

    /**
     * @static
     *
     * @param $type
     *
     * @return mixed
     */
    public static function getTypeFolder($type)
    {
        $typearray = FolderFactory::getTypeArray();
        return $typearray[$type];
    }
}

?>