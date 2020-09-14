<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Helper\File\SVGExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

/**
 * @deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Use the symfony/mime component instead.
 */
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
