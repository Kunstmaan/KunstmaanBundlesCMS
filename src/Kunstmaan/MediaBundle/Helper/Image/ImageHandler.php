<?php

namespace Kunstmaan\MediaBundle\Helper\Image;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Symfony\Component\HttpFoundation\File\File;

/**
 * FileHandler
 */
class ImageHandler extends FileHandler
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Image Handler';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'image';
    }

    /**
     * @return bool
     */
    public function canHandle($object)
    {
        if (parent::canHandle($object) && ($object instanceof File || strncmp($object->getContentType(), 'image', 5) === 0)) {
            return true;
        }

        return false;
    }

    public function getShowTemplate(Media $media)
    {
        return '@KunstmaanMedia/Media/Image/show.html.twig';
    }

    /**
     * @param Media  $media    The media entity
     * @param string $basepath The base path
     *
     * @return string
     */
    public function getImageUrl(Media $media, $basepath)
    {
        return $basepath . $media->getUrl();
    }

    public function prepareMedia(Media $media)
    {
        parent::prepareMedia($media);

        if ($media->getContent()) {
            $imageInfo = getimagesize($media->getContent());

            $width = $height = null;
            if (false !== $imageInfo) {
                [$width, $height] = $imageInfo;
            }

            $media
                ->setMetadataValue('original_width', $width)
                ->setMetadataValue('original_height', $height);
        }
    }
}
