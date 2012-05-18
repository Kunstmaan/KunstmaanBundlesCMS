<?php

namespace Kunstmaan\MediaBundle\Helper\Provider;

use Gaufrette\Filesystem;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Cdn\CdnInterface;
use Kunstmaan\MediaBundle\Helper\Generator\PathGeneratorInterface;
use Kunstmaan\MediaBundle\Helper\Generator\UuidGeneratorInterface;
use Kunstmaan\MediaBundle\Helper\Generator\ExtensionGuesser;

abstract class AbstractProvider implements ProviderInterface
{
    /* @var array */
    protected $formats;

    /* @var string */
    protected $name;

    /* @var Filesystem */
    protected $filesystem;

    /* @var PathGeneratorInterface */
    protected $pathGenerator;

    /* @var UuidGeneratorInterface */
    protected $uuidGenerator;

    /* @var CdnInterface */
    protected $cdn;

    /* @var string */
    protected $template;

    public function __construct($name, CdnInterface $cdn, Filesystem $filesystem, PathGeneratorInterface $pathGenerator, UuidGeneratorInterface $uuidGenerator)
    {
        $this->name          = $name;
        $this->cdn           = $cdn;
        $this->filesystem    = $filesystem;
        $this->pathGenerator = $pathGenerator;
        $this->uuidGenerator = $uuidGenerator;
        $this->formats       = array();
    }

    /**
     * @param string $name
     * @param array $format
     *
     * @return void
     */
    public function addFormat($name, array $format)
    {
        $this->formats[$name] = $format;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasFormat($name)
    {
        return array_key_exists($name, $this->formats);
    }

    /**
     * @param string $name
     *
     * @return string|boolean
     */
    public function getFormat($name)
    {
        return $this->hasFormat($name) ? $this->formats[$name] : FALSE;
    }

    /**
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @param array $formats
     *
     * @return void
     */
    public function setFormats(array $formats)
    {
        $this->formats = $formats;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Media $media
     *
     * @return string
     */
    public function generatePath(Media $media)
    {
        return $this->pathGenerator->generatePath($media);
    }

    /**
     * @param Media $media
     *
     * @return string
     */
    public function generateUuid(Media $media)
    {
        return $this->uuidGenerator->generateUuid($media);
    }

    /**
     * @return \Gaufrette\Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @return \Ano\Bundle\MediaBundle\Cdn\CdnInterface
     */
    public function getCdn()
    {
        return $this->cdn;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * {@inheritDoc}
     */
    public function renderRaw(Media $media, $format = NULL, array $options = array())
    {
        return $this->getMediaUrl($media, $format);
    }


    public function generateRelativePath(Media $media, $format = NULL)
    {
        return sprintf('%s/%s_%s.%s', $this->generatePath($media), $media->getUuid(), $format, ExtensionGuesser::guess($media->getContentType()));
    }

    /**
     * {@inheritDoc}
     */
    public function updateMedia(Media $media)
    {
        $this->saveMedia($media);
    }
}