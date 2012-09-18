<?php

namespace Kunstmaan\MediaBundle\Helper\Cdn;

/**
 * RemoteServerCdn
 */
class RemoteServerCdn implements CdnInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get the full path to the media
     * In most cases that would be the CDN base path/URL
     * + the internal media relative path
     *
     * @param string $path
     *
     * @return string
     */
    public function getFullPath($path)
    {
        return sprintf('%s/%s', $this->baseUrl, $path);
    }

    /**
     * {@inheritDoc}
     */
    public function flush($resource)
    {
        return;
    }

}
