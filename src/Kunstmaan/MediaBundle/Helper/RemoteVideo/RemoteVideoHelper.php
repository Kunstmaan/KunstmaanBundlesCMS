<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteVideo;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Remote\AbstractRemoteHelper;
use Kunstmaan\MediaBundle\Helper\Remote\RemoteInterface;

/**
 * Kunstmaan\MediaBundle\Entity\Video
 * Class that defines a video in the system
 */
class RemoteVideoHelper extends AbstractRemoteHelper implements RemoteInterface
{
    /**
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        parent::__construct($media);
        $this->media->setContentType(RemoteVideoHandler::CONTENT_TYPE);
    }
}
