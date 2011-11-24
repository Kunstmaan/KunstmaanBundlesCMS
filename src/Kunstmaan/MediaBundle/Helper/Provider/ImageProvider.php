<?php

namespace Kunstmaan\KMediaBundle\Helper\Provider;

use Kunstmaan\KMediaBundle\Entity\Media;
use Kunstmaan\KMediaBundle\Helper\Manipulator\ImageManipulatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Kunstmaan\KMediaBundle\Helper\Generator\ExtensionGuesser;

class ImageProvider extends FileProvider
{
    /* @var \Ano\Bundle\MediaBundle\Util\Image\ImageManipulatorInterface */
    protected $imageManipulator;


    /**
     * {@inheritDoc}
     */
    public function prepareMedia(Media $media)
    {
        parent::prepareMedia($media);

        $metadata = $media->getMetadata();
        list($metadata['width'], $metadata['height']) = @getimagesize($media->getContent()->getRealPath());
        $media->setMetadata($metadata);
    }

    /**
     * {@inheritDoc}
     */
    public function saveMedia(Media $media)
    {
        parent::saveMedia($media);
        $this->generateFormats($media);
    }

    public function generateFormats(Media $media)
    {
        $originalFile = $this->getOriginalFile($media);

        foreach($this->formats as $format => $options) {
            $this->imageManipulator->resize(
                $media,
                $originalFile,
                $this->filesystem->get($this->generateRelativePath($media, $format), true),
                $options
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRenderOptions(Media $media, $format, array $options = array())
    {
        return $options;
    }

    public function setImageManipulator(ImageManipulatorInterface $imageManipulator)
    {
        $this->imageManipulator = $imageManipulator;
    }
}