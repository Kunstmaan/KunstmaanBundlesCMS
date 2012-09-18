<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * DefaultPathGenerator
 */
class DefaultPathGenerator implements PathGeneratorInterface
{

    /**
     * @param Media $media
     *
     * @return string
     */
    public function generatePath(Media $media)
    {
        return sprintf('%s', $media->getContext());
    }

}