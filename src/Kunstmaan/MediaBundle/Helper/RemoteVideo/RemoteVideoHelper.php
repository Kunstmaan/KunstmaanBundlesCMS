<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteVideo;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * Kunstmaan\MediaBundle\Entity\Video
 * Class that defines a video in the system
 */
class RemoteVideoHelper
{

    /**
     * @var Media
     */
    protected $media;

    /**
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
        $this->media->setContentType(RemoteVideoHandler::CONTENT_TYPE);
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
     * @param string $code
     *
     * @return RemoteVideoHelper
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
     * @param string $url
     *
     * @return RemoteVideoHelper
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
     * @param string $type
     *
     * @return RemoteVideoHelper
     */
    public function setType($type)
    {
        $this->media->setMetadataValue('type', $type);

        return $this;
    }

}
