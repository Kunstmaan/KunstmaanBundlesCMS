<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Helper\File\SVGMimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

@trigger_error(sprintf('The "%s" class is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Use the symfony/mime component instead.', __CLASS__), E_USER_DEPRECATED);

/**
 * @deprecated This class is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Use the symfony/mime component instead.
 */
class MimeTypeGuesserFactory implements MimeTypeGuesserFactoryInterface
{
    /**
     * Should return a mime type guesser instance, used for file uploads
     *
     * NOTE: If you override this, you'll probably still have to register the SVGMimeTypeGuesser as last guesser...
     *
     * @return MimeTypeGuesserInterface
     */
    public function get()
    {
        $guesser = MimeTypeGuesser::getInstance();
        $guesser->register(new SVGMimeTypeGuesser());

        return $guesser;
    }
}
