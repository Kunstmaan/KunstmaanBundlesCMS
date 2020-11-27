<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteSlide;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Remote\AbstractRemoteHelper;
use Kunstmaan\MediaBundle\Helper\Remote\RemoteInterface;

/**
 * Kunstmaan\MediaBundle\Entity\Video
 * Defines a video in the system
 */
class RemoteSlideHelper extends AbstractRemoteHelper implements RemoteInterface
{
    public function __construct(Media $media)
    {
        parent::__construct($media);
        $this->media->setContentType(RemoteSlideHandler::CONTENT_TYPE);
    }
}
