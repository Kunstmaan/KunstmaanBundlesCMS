<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

class DefaultUuidGenerator implements UuidGeneratorInterface
{
    public function generateUuid(Media $media)
    {
        return uniqid();
    }
}