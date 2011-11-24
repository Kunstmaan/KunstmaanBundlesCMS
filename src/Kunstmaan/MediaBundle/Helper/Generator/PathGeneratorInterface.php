<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

interface PathGeneratorInterface
{
    public function generatePath(Media $media);
}