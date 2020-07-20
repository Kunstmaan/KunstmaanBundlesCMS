<?php

namespace Kunstmaan\MediaBundle\Helper\Image;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\ExtensionGuesserFactoryInterface;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * FileHandler
 */
class ImageHandler extends FileHandler
{
    protected $aviaryApiKey;

    /**
     * @param int                              $priority
     * @param MimeTypeGuesserFactoryInterface  $mimeTypeGuesserFactory
     * @param ExtensionGuesserFactoryInterface $extensionGuesserFactoryInterface
     * @param string                           $aviaryApiKey                     The aviary key
     */
    public function __construct($priority, MimeTypeGuesserFactoryInterface $mimeTypeGuesserFactory, ExtensionGuesserFactoryInterface $extensionGuesserFactoryInterface, $aviaryApiKey)
    {
        parent::__construct($priority, $mimeTypeGuesserFactory, $extensionGuesserFactoryInterface);
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
     * @param mixed $object
     *
     * @return bool
     */
    public function canHandle($object)
    {
        if (parent::canHandle($object) && ($object instanceof File || strncmp($object->getContentType(), 'image', 5) === 0)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * @param Media $media
     */
    public function prepareMedia(Media $media)
    {
        parent::prepareMedia($media);

        if ($media->getContent()) {
            $imageInfo = getimagesize($media->getContent());

            $width = $height = null;
            if (false !== $imageInfo) {
                list($width, $height) = $imageInfo;
            }

            $media
                ->setMetadataValue('original_width', $width)
                ->setMetadataValue('original_height', $height);
        }
    }
}
