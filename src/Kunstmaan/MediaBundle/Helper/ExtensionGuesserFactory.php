<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Helper\File\SVGExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

class ExtensionGuesserFactory implements ExtensionGuesserFactoryInterface
{
    /**
     * Should return an extension guesser instance, used for file uploads
     *
     * NOTE: If you override this, you'll probably still have to register the SVGExtensionGuesser as last guesser...
     *
     * @return ExtensionGuesserInterface
     */
    public function get()
    {
        $guesser = ExtensionGuesser::getInstance();
        $guesser->register(new SVGExtensionGuesser());

        return $guesser;
    }
}