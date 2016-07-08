<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

/**
 * SVGMimeTypeGuesser
 *
 * @package Kunstmaan\MediaBundle\Helper\File
 */
class SVGExtensionGuesser implements ExtensionGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guess($mimeType)
    {
       if($mimeType === 'image/svg+xml') {
           return 'svg';
       }
        
        return null;
    }
}