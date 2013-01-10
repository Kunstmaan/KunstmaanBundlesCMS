<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteSlide;

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
        return $this->media->getMetadataValue('code');
    }

    /**
     * Set code
     * @param string $code
     *
     * @return RemoteSlideHelper
     */
    public function setCode($code)
    {
        $this->media->setMetadataValue('code', $code);

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl()
    {
        return $this->media->getMetadataValue('thumbnail_url');
    }

    /**
     * Set thumbnail url
     * @param string $url
     *
     * @return RemoteSlideHelper
     */
    public function setThumbnailUrl($url)
    {
        $this->media->setMetadataValue('thumbnail_url', $url);

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->media->getMetadataValue('type');
    }

    /**
     * Set type
     * @param string $type
     *
     * @return RemoteSlideHelper
     */
    public function setType($type)
    {
        $this->media->setMetadataValue('type', $type);

        return $this;
    }

}
