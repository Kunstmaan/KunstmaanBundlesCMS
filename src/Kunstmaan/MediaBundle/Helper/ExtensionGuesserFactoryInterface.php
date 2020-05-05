<?php

namespace Kunstmaan\MediaBundle\Helper;

use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

@trigger_error(sprintf('The "%s" class is deprecated since KunstmaanMediaBundle 5.6 and will be removed in KunstmaanMediaBundle 6.0. Use the symfony/mime component instead.', __CLASS__), E_USER_DEPRECATED);

/**
 * @deprecated this class is deprecated since KunstmaanMediaBundle 5.6 and will be removed in KunstmaanMediaBundle 6.0
 */
interface ExtensionGuesserFactoryInterface
{
    /**
     * @return ExtensionGuesserInterface
     */
    public function get();
}
