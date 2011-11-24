<?php

namespace Kunstmaan\KMediaBundle\Helper\Cdn;

class RemoteServerCdn implements CdnInterface
{
    /* @var string */
    protected $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * {@inheritDoc}
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