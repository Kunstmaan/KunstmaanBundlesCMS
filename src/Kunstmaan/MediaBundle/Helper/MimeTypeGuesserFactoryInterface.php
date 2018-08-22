<?php

namespace Kunstmaan\MediaBundle\Helper;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

interface MimeTypeGuesserFactoryInterface
{
    /**
     * @return MimeTypeGuesserInterface
     */
    public function get();
}
