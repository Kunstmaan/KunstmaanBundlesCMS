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
     * @param Filesystem          $filesystem
     * @param RequestContext      $requestContext
     * @param string              $webRootDir
     * @param string              $cachePrefix
     * @param FilterConfiguration $filterConfig
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

    /**
     * {@inheritdoc}
     */
    public function resolve($path, $filter)
    {
        return sprintf('%s/%s',
            $this->getBaseUrl(),
            $this->getFileUrl($path, $filter)
        );
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
}
