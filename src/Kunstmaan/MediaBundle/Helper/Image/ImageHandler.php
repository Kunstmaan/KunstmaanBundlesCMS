<?php

namespace Kunstmaan\MediaBundle\Helper\Image;

use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Symfony\Component\HttpFoundation\File\MimeType\FileBinaryMimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * FileHandler
 */
class ImageHandler extends FileHandler
{

    protected $aviaryApiKey;

    /**
     * @param ImageManipulatorInterface $imageManipulator The image manipulator
     * @param string                    $aviaryApiKey     The aviary key
     */
    public function __construct($aviaryApiKey)
    {
        parent::__construct();
        $this->aviaryApiKey = $aviaryApiKey;
    }

    /**
     * @return string
     */
    public function getAviaryApiKey()
    {
        return $this->aviaryApiKey;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "Image Handler";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'image';
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function canHandle(Media $media)
    {
        if (parent::canHandle($media) && strpos($media->getContentType(), 'image') === 0) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media\Image:show.html.twig';
    }

    /**
     * @param Media  $media    The media entity
     * @param string $basepath The base path
     *
     * @return string
     */
    public function getImageUrl(Media $media, $basepath)
    {
        $localPath = '/uploads/media/'.$media->getUrl();

        return $basepath . $localPath;
    }

}