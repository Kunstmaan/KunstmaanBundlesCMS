<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * UuidGeneratorInterface
 */
interface UuidGeneratorInterface
{
    /**
     * @param Media $media
     */
    public function generateUuid(Media $media);
}