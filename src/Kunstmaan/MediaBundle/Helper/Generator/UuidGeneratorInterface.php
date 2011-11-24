<?php

namespace Kunstmaan\KMediaBundle\Helper\Generator;

use Kunstmaan\KMediaBundle\Entity\Media;

interface UuidGeneratorInterface
{
    public function generateUuid(Media $media);
}