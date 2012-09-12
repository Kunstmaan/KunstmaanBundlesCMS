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
     * {@inheritdoc}
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
