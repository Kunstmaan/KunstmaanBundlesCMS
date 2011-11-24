<?php

namespace Kunstmaan\KMediaBundle\Helper\Generator;

use Kunstmaan\KMediaBundle\Entity\Media;

class DefaultPathGenerator implements PathGeneratorInterface
{
    public function generatePath(Media $media)
    {
        return sprintf('%s', $media->getContext());
    }

}