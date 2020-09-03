<?php

namespace Kunstmaan\MediaBundle\Helper;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

@trigger_error(sprintf('The "%s" class is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Use the symfony/mime component instead.', __CLASS__), E_USER_DEPRECATED);

/**
 * @deprecated This class is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Use the symfony/mime component instead.
 */
interface MimeTypeGuesserFactoryInterface
{
    /**
     * @return MimeTypeGuesserInterface
     */
    public function get();
}
