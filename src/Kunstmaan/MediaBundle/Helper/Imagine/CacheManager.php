<?php

namespace Kunstmaan\MediaBundle\Helper\Imagine;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CacheManager extends \Liip\ImagineBundle\Imagine\Cache\CacheManager
{
    /**
     * {@inheritdoc}
     */
    public function generateUrl($path, $filter, array $runtimeConfig = [], $resolver = null, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        $originalPath = $path;
        $filterConf = $this->filterConfig->get($filter);
        $path = $this->changeFileExtension(ltrim($path, '/'), $filterConf['format']);

        $params = [
            'path' => ltrim($path, '/'),
            'filter' => $filter,
        ];

        if ($resolver) {
            $params['resolver'] = $resolver;
        }

        if (empty($runtimeConfig)) {
            $filterUrl = $this->router->generate('liip_imagine_filter', $params, $referenceType);
        } else {
            $params['filters'] = $runtimeConfig;
            $params['hash'] = $this->signer->sign($originalPath, $runtimeConfig);

            $filterUrl = $this->router->generate('liip_imagine_filter_runtime', $params, $referenceType);
        }

        return $filterUrl;
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
    public function getBrowserPath($path, $filter, array $runtimeConfig = [], $resolver = null, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        $infoPath = parse_url($path, PHP_URL_PATH);
        $info = pathinfo($infoPath);
        $url = parent::getBrowserPath($path, $filter, $runtimeConfig, $resolver, $referenceType);
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
     *
     * @return string
     */
    private function changeFileExtension($path, $format)
    {
        if (!$format) {
            return $path;
        }

        $info = pathinfo($path);

        return $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $format;
    }
}
