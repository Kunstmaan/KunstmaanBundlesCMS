<?php

namespace Kunstmaan\KMediaBundle\Helper\Generator;

use Kunstmaan\KMediaBundle\Entity\Media;

class DefaultUuidGenerator implements UuidGeneratorInterface
{
    public function generateUuid(Media $media)
    {
        return uniqid();
    }
}