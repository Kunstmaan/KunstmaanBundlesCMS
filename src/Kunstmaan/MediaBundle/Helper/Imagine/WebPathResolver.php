<?php

namespace Kunstmaan\MediaBundle\Helper\Imagine;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RequestContext;

class WebPathResolver extends \Liip\ImagineBundle\Imagine\Cache\Resolver\WebPathResolver
{
    /**
     * @var FilterConfiguration
     */
    protected $filterConfig;

    /**
     * @param string $webRootDir
     * @param string $cachePrefix
     */
    public function __construct(Filesystem $filesystem, RequestContext $requestContext, $webRootDir, $cachePrefix = 'media/cache', FilterConfiguration $filterConfig)
    {
        parent::__construct($filesystem, $requestContext, $webRootDir, $cachePrefix);

        $this->filterConfig = $filterConfig;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileUrl($path, $filter)
    {
        $filterConf = $this->filterConfig->get($filter);
        $path = $this->changeFileExtension($path, $filterConf['format']);

        return parent::getFileUrl($path, $filter);
    }

    protected function getFilePath($path, $filter)
    {
        $filterConf = $this->filterConfig->get($filter);
        $path = $this->changeFileExtension($path, $filterConf['format']);
        $fullPath = $this->getFullPath($path, $filter);

        return $this->webRoot . '/' . $fullPath;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($path, $filter)
    {
        return sprintf('%s/%s', $this->getBaseUrl(), $this->getFileUrl($path, $filter));
    }

    /**
     * @param string $path
     * @param string $format
     *
     * @return string
     */
    private function changeFileExtension($path, $format)
    {
        if (!$format) {
            return $path;
        }

        $info = pathinfo($path);
        $path = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $format;

        return $path;
    }

    /**
     * Copy from \Liip\ImagineBundle\Imagine\Cache\Resolver\WebPathResolver::getFullPath
     */
    private function getFullPath($path, $filter)
    {
        // crude way of sanitizing URL scheme ("protocol") part
        $path = str_replace('://', '---', $path);

        return $this->cachePrefix . '/' . $filter . '/' . ltrim($path, '/');
    }
}
