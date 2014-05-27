<?php

namespace Kunstmaan\AdminBundle\Helper\VersionCheck;

use Doctrine\Common\Cache\Cache;
use Guzzle\Http\Client;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VersionChecker
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $webserviceUrl;

    /**
     * @var int
     */
    private $cacheTimeframe;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     * @param Cache $cache
     */
    public function __construct(ContainerInterface $container, Cache $cache)
    {
        $this->container = $container;
        $this->cache = $cache;

        $this->webserviceUrl = $this->container->getParameter('version_checker.url');
        $this->cacheTimeframe = $this->container->getParameter('version_checker.timeframe');
        $this->enabled = $this->container->getParameter('version_checker.enabled');
    }

    /**
     * Check that the version check is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Check if we recently did a version check, if not do one now.
     */
    public function periodicallyCheck()
    {
        if (!$this->enabled) return;

        $data = $this->cache->fetch('version_check');
        if (!is_array($data)) {
            $this->check();
        }
    }

    /**
     * Get the version details via webservice.
     */
    public function check()
    {
        if (!$this->enabled) return;

        $jsonData = json_encode(array(
            'host' => $this->container->get('request')->getHttpHost(),
            'installed' => filectime($this->container->get('kernel')->getRootDir().'/console'),
            'bundles' => $this->parseComposer(),
            'project' => $this->container->getParameter('websitetitle')
        ));

        try {
            $client = new Client($this->webserviceUrl, array(
                'curl.options' => array(
                    CURLOPT_TIMEOUT        => 3,
                    CURLOPT_CONNECTTIMEOUT => 1
                )
            ));
            $request = $client->post('', null, $jsonData);
            $call = $request->send();
            $data = $call->json();

            // Save the result in the cache to make sure we don't do the check too often
            $this->cache->save('version_check', $data, $this->cacheTimeframe);

            return $data;
        } catch (\RuntimeException $e) {
            // We did not receive valid json
            return false;
        }
    }

    /**
     * Parse the composer.lock file to get the current used versions of the kunstmaan bundles.
     *
     * @return array
     * @throws Exception\ParseException
     */
    private function parseComposer()
    {
        $bundles = array();

        $rootPath = dirname($this->container->get('kernel')->getRootDir());
        $composerPath = "{$rootPath}/composer.lock";

        if (file_exists($composerPath)) {
            $result = json_decode(file_get_contents($composerPath), true);
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    // No parse errors, we get a list of installed packages
                    if (array_key_exists('packages', $result) && is_array($result['packages'])) {
                        $packages = $result['packages'];
                    } else {
                        $this->get('translator')->trans('settings.version.error_parsing_composer');
                    }
                    break;
                default:
                    throw new ParseException($this->container->get('translator')->trans('settings.version.error_parsing_composer').' (#'.json_last_error().')');
                    break;
            }

            if (is_array($packages)) {
                foreach ($packages as $package) {
                    if (!strncmp($package['name'], 'kunstmaan/', strlen('kunstmaan/'))) {
                        $bundles[] = array(
                            'name' => $package['name'],
                            'version' => $package['version'],
                            'reference' => $package['source']['reference']
                        );
                    }
                }
            }
        } else {
            throw new ParseException($this->container->get('translator')->trans('settings.version.composer_lock_not_found'));
        }

        return $bundles;
    }
}