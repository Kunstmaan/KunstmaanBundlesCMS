<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Helper\File\SVGExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

@trigger_error(sprintf('The "%s" class is deprecated since KunstmaanMediaBundle 5.6 and will be removed in KunstmaanMediaBundle 6.0. Use the symfony/mime component instead.', __CLASS__), E_USER_DEPRECATED);

/**
 * @deprecated this class is deprecated since KunstmaanMediaBundle 5.6 and will be removed in KunstmaanMediaBundle 6.0
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
