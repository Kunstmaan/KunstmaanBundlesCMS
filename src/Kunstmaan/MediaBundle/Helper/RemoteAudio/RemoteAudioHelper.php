<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteAudio;

use Kunstmaan\MediaBundle\Helper\Remote\AbstractRemoteHelper;
use Kunstmaan\MediaBundle\Helper\Remote\RemoteInterface;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * Kunstmaan\MediaBundle\Entity\Audio
 * Class that defines audio in the system
 */
class RemoteAudioHelper extends AbstractRemoteHelper implements RemoteInterface
{
    /**
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        parent::__construct($media);
        $this->media->setContentType(RemoteAudioHandler::CONTENT_TYPE);
    }
}
