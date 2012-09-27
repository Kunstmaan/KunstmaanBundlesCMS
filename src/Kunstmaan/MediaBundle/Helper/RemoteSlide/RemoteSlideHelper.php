<?php

namespace Kunstmaan\MediaBundle\Entity\Helper\RemoteVideo;
use Kunstmaan\MediaBundle\Helper\RemoteSlide\RemoteSlideHandler;

use Kunstmaan\MediaBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\Video
 * Class that defines a video in the system
 */
class RemoteSlideHelper
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
        $this->media->setContentType(RemoteSlideHandler::CONTENT_TYPE);
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
        return $this->metadata['uuid'];
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
     * Get type
     *
     * @return string
     */
    public function getType()
    {
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

    /**
     * {@inheritDoc}
     */
    public function updateMedia(Media $media)
    {
        $this->saveMedia($media);
    }

}
