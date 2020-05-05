<?php

namespace Kunstmaan\MediaBundle\Helper\File;

use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

@trigger_error(sprintf('The "%s" class is deprecated since KunstmaanMediaBundle 5.6 and will be removed in KunstmaanMediaBundle 6.0, use the "symfony/mime" component instead.', __CLASS__), E_USER_DEPRECATED);

/**
 * @deprecated this class is deprecated since KunstmaanMediaBundle 5.6 and will be removed in KunstmaanMediaBundle 6.0
 */
class SVGExtensionGuesser implements ExtensionGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guess($mimeType)
    {
        if ($mimeType === 'image/svg+xml') {
            return 'svg';
        }

        return null;
    }
}
