<?php

namespace Kunstmaan\MediaBundle\Helper\Generator;

use Kunstmaan\MediaBundle\Entity\Media;

/**
 * DefaultUuidGenerator
 */
class DefaultUuidGenerator implements UuidGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateUuid(Media $media)
    {
        return uniqid();
    }
}