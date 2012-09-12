<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * PathGeneratorInterface
 */
interface PathGeneratorInterface
{
    /**
     * @param Media $media
     */
    public function generatePath(Media $media);
}