<?php

namespace Kunstmaan\KMediaBundle\Helper\Cdn;

interface CdnInterface
{
    /**
     * Get the full path to the media
     * In most cases that would be the CDN base path/URL
     * + the internal media relative path
     *
     * @param string $path
     * @return void
     */
    public function getFullPath($path);

    /**
     * Ask the CDN to flush the resource
     *
     * @param string $resource
     * @return void
     */
    public function flush($resource);
}