<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * DefaultUuidGenerator
 */
class DefaultUuidGenerator implements UuidGeneratorInterface
{
    /**
     * @param Media $media
     *
     * @return string
     */
    public function generateUuid(Media $media)
    {
        return uniqid();
    }
}