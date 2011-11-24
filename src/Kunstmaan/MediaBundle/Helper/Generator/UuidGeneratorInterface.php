<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

interface UuidGeneratorInterface
{
    public function generateUuid(Media $media);
}