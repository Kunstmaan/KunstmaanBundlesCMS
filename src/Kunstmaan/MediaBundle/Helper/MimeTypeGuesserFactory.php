<?php

namespace Kunstmaan\MediaBundle\Helper;

use Kunstmaan\MediaBundle\Helper\File\SVGMimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

class MimeTypeGuesserFactory implements MimeTypeGuesserFactoryInterface
{
    /**
     * Should return a mime type guesser instance, used for file uploads
     *
     * NOTE: If you override this, you'll probably still have to register the SVGMimeTypeGuesser as last guesser...
     *
     * @return MimeTypeGuesser
     */
    public function get()
    {
        $guesser = MimeTypeGuesser::getInstance();
        $guesser->register(new SVGMimeTypeGuesser());

        return $guesser;
    }
}
