<?php

namespace Kunstmaan\MediaBundle\Helper;

use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

/**
 * @deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Use the symfony/mime component instead.
 */
interface ExtensionGuesserFactoryInterface
{
    /**
     * @return ExtensionGuesserInterface
     */
    public function get();
}
