<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteVideo;
use Kunstmaan\MediaBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

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
        //TODO: update location here?
        return $this->media;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->media->metadata['code'];
    }

    /**
     * Set code
     * @param string $code
     *
     * @return RemoteVideoHelper
     */
    public function setCode($code)
    {
        $this->media->metadata['code'] = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl()
    {
        if (isset($this->media->metadata['thumbnail_url'])) {
            return $this->media->metadata['thumbnail_url'];
        }

        return null;
    }

    /**
     * Set thumbnail url
     * @param string $url
     *
     * @return RemoteVideoHelper
     */
    public function setThumbnailUrl($url)
    {
        $this->media->metadata['thumbnail_url'] = $url;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        if (empty($this->media->metadata['type'])) {
            return null;
        }

        return $this->media->metadata['type'];
    }

    /**
     * Set type
     * @param string $type
     *
     * @return RemoteVideoHelper
     */
    public function setType($type)
    {
        $this->media->metadata['type'] = $type;

        return $this;
    }

}
