<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteAudio;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Remote\AbstractRemoteHelper;
use Kunstmaan\MediaBundle\Helper\Remote\RemoteInterface;

/**
 * Kunstmaan\MediaBundle\Entity\Audio
 * Defines audio in the system
 */
class RemoteAudioHelper extends AbstractRemoteHelper implements RemoteInterface
{
    public function __construct(Media $media)
    {
        parent::__construct($media);
        $this->media->setContentType(RemoteAudioHandler::CONTENT_TYPE);
    }
}
