<?php

namespace Kunstmaan\KMediaBundle\Helper\Manipulator;

use Kunstmaan\KMediaBundle\Entity\Media;
use Kunstmaan\KMediaBundle\Helper\Generator\ExtensionGuesser;

use Imagine\Image\ImagineInterface,
    Imagine\Image\ImageInterface,
    Imagine\Image\Box;

use Gaufrette\File;

class ImagineImageManipulator implements ImageManipulatorInterface
{
    /* @var \Imagine\ImagineInterface */
    protected $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * {@inheritDoc}
     */
    public function resize(Media $media, File $fromFile, File $toFile, $options = array())
    {
        if (!array_key_exists('quality', $options)) {
            $options['quality'] = 100;
        }

        $mode = isset($options['mode']) ? $options['mode'] : self::RESIZE_MODE_OUTBOUND;
        $width = isset($options['width']) ? (int)$options['width'] : null;
        $height = isset($options['height']) ? (int)$options['height'] : null;

        if (!is_numeric($width) && !is_numeric($height)) {
            throw new \InvalidArgumentException('You must specify at least a width and/or an height value');
        }
        
        $metadata = $media->getMetadata();
        if (null !== $width && null == $height) {
            $height = (int)($width * $metadata['height'] / $metadata['width']);
        }
        else if (null == $width) {
            $width = (int)($height * $metadata['width'] / $metadata['height']);
        }

        switch($mode) {
            case self::RESIZE_MODE_OUTBOUND:
                $mode = ImageInterface::THUMBNAIL_OUTBOUND;
            break;

            case self::RESIZE_MODE_INSET:
                $mode = ImageInterface::THUMBNAIL_INSET;
            break;

            default:
                $mode = ImageInterface::THUMBNAIL_OUTBOUND;
        }
        
        $image = $this->imagine->load($fromFile->getContent());
        $output = $image
            ->thumbnail(new Box($width, $height), $mode)
            ->get(ExtensionGuesser::guess($media->getContentType()), $options);

        $toFile->setContent($output);
    }


}