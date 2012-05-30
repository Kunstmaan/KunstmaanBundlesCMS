<?php

namespace Kunstmaan\MediaBundle\Helper\Event;

use Symfony\Component\EventDispatcher\Event;

use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Kunstmaan\AdminNodeBundle\Entity\Node;

class MediaEvent extends Event
{

    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     */
    protected $media;

    /**
     * @var \Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata
     */
    protected $metadata;

    /**
     * @param \Kunstmaan\MediaBundle\Entity\Media $media
     * @param \Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata $metadata
     */
    public function __construct(\Kunstmaan\MediaBundle\Entity\Media $media, \Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata $metadata = null)
    {
        $this->media    = $media;
        $this->metadata = $metadata;
    }

    /**
     * @param $media
     *
     * @return MediaEvent
     */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param $metadata
     *
     * @return MediaEvent
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @return \Kunstmaan\MediaBundle\Entity\AbstractMediaMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

}
