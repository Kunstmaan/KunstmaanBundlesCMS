<?php

namespace Kunstmaan\MediaBundle\Helper\Remote;

use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * Kunstmaan\MediaBundle\Entity\Abstract
 * Class that defines a remote entity in the system
 */
abstract class AbstractRemoteHelper
{
    /** @var Media $media */
    protected $media;

    /**
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->media->getName();
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->media->setName($name);
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->media->getCopyright();
    }

    /**
     * @param string $copyright
     */
    public function setCopyright($copyright)
    {
        $this->media->setCopyright($copyright);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->media->getDescription();
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->media->setDescription($description);
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->media->getMetadataValue('code');
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->media->setMetadataValue('code', $code);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrl()
    {
        return $this->media->getMetadataValue('thumbnail_url');
    }

    /**
     * Set thumbnail url
     *
     * @param string $url
     *
     * @return self
     */
    public function setThumbnailUrl($url)
    {
        $this->media->setMetadataValue('thumbnail_url', $url);

        return $this;
    }

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->media->getMetadataValue('type');
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->media->setMetadataValue('type', $type);

        return $this;
    }

    /**
     * @return Folder
     */
    public function getFolder()
    {
        return $this->media->getFolder();
    }

    /**
     * @param Folder $folder
     */
    public function setFolder(Folder $folder)
    {
        $this->media->setFolder($folder);
    }
}
