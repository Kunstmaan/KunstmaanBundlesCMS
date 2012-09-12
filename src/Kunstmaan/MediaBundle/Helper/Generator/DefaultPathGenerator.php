<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * DefaultPathGenerator
 */
class DefaultPathGenerator implements PathGeneratorInterface
{

    /**
     * {@inheritdoc}
     */
    public function generatePath(Media $media)
    {
        return sprintf('%s', $media->getContext());
    }

}