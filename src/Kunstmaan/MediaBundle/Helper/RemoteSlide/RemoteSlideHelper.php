<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteSlide;

use Kunstmaan\MediaBundle\Helper\Remote\AbstractRemoteHelper;
use Kunstmaan\MediaBundle\Helper\Remote\RemoteInterface;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * Kunstmaan\MediaBundle\Entity\Video
 * Class that defines a video in the system
 */
class RemoteSlideHelper extends AbstractRemoteHelper implements RemoteInterface
{
    /**
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        parent::__construct($media);
        $this->media->setContentType(RemoteSlideHandler::CONTENT_TYPE);
    }
}
