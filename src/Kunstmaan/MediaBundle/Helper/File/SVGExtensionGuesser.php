<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

/**
 * SVGMimeTypeGuesser
 */
class SVGExtensionGuesser implements ExtensionGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guess($mimeType)
    {
        if ($mimeType === 'image/svg+xml') {
            return 'svg';
        }

        return null;
    }
}
