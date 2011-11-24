<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

class DefaultPathGenerator implements PathGeneratorInterface
{
    public function generatePath(Media $media)
    {
        return sprintf('%s', $media->getContext());
    }

}