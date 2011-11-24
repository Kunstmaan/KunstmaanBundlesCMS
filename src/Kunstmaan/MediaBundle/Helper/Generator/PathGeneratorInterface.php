<?php

namespace Kunstmaan\KMediaBundle\Helper\Generator;

use Kunstmaan\KMediaBundle\Entity\Media;

interface PathGeneratorInterface
{
    public function generatePath(Media $media);
}