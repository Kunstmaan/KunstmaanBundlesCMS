<?php

namespace Kunstmaan\MediaBundle\Helper\Imagine;

class CacheManager extends \Liip\ImagineBundle\Imagine\Cache\CacheManager
{
    /**
     * {@inheritdoc}
     */
    public function generateUrl($path, $filter, array $runtimeConfig = array(), $resolver = null)
    {
        $filterConf = $this->filterConfig->get($filter);
        $path = $this->changeFileExtension(ltrim($path, '/'), $filterConf['format']);

        return parent::generateUrl($path, $filter, $runtimeConfig, $resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($path, $filter, $resolver = null)
    {
        $filterConf = $this->filterConfig->get($filter);
        $path = $this->changeFileExtension($path, $filterConf['format']);

        return parent::resolve($path, $filter, $resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getBrowserPath($path, $filter, array $runtimeConfig = array(), $resolver = null)
    {
        $infoPath = parse_url($path, PHP_URL_PATH);
        $info = pathinfo($infoPath);
        $url = parent::getBrowserPath($path, $filter, $runtimeConfig, $resolver);
        $newPath = parse_url($url, PHP_URL_PATH);
        $newInfo = pathinfo($newPath);
        if ($info['extension'] != $newInfo['extension']) {
            $query = parse_url($url, PHP_URL_QUERY);
            $url .= ($query ? '&' : '?') . 'originalExtension=' . $info['extension'];
        }

        return $url;
    }

    /**
     * @param string $path
     * @param string $format
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
