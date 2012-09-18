<?php

namespace Kunstmaan\MediaBundle\Event;

use Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata;
use Symfony\Component\EventDispatcher\Event;

use Kunstmaan\MediaBundle\Entity\Media;

use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Kunstmaan\AdminNodeBundle\Entity\Node;

/**
 * MediaEvent
 */
class MediaEvent extends Event
{

    /**
     * @var Media
     */
    protected $media;

    /**
     * @var AbstractMediaMetadata
     */
    protected $metadata;

    /**
     * @param Media                 $media    Media
     * @param AbstractMediaMetadata $metadata Metadata
     */
    public function __construct(Media $media, AbstractMediaMetadata $metadata = null)
    {
        $this->media    = $media;
        $this->metadata = $metadata;
    }

    /**
     * @param Media $media
     *
     * @return MediaEvent
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param AbstractMediaMetadata $metadata
     *
     * @return MediaEvent
     */
    public function setMetadata(AbstractMediaMetadata $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return AbstractMediaMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

}
