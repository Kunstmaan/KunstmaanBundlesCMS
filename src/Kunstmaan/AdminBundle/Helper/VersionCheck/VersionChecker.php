<?php

namespace Kunstmaan\AdminBundle\Helper\VersionCheck;

use Doctrine\Common\Cache\Cache;
use Guzzle\Http\Client;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Version checker
 */
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
     *
     * @return void
     */
    public function periodicallyCheck()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $data = $this->cache->fetch('version_check');
        if (!is_array($data)) {
            $this->check();
        }
    }

    /**
     * Get the version details via webservice.
     *
     * @return mixed A list of bundles if available.
     */
    public function check()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $jsonData = json_encode(array(
            'host' => $this->container->get('request_stack')->getCurrentRequest()->getHttpHost(),
            'installed' => filectime($this->container->get('kernel')->getRootDir().'/../bin/console'),
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
     * Returns the absolute path to the composer.lock file.
     *
     * @return string
     */
    protected function getLockPath()
    {
        $rootPath = dirname($this->container->get('kernel')->getRootDir());

        return $rootPath.'/composer.lock';
    }

    /**
     * Returns a list of composer packages.
     *
     * @return array
     * @throws Exception\ParseException
     */
    protected function getPackages()
    {
        $translator = $this->container->get('translator');
        $errorMessage = $translator->trans('settings.version.error_parsing_composer');

        $composerPath = $this->getLockPath();
        if (!file_exists($composerPath)) {
            throw new ParseException(
                $translator->trans('settings.version.composer_lock_not_found')
            );
        }

        $json = file_get_contents($composerPath);
        $result = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseException($errorMessage.' (#'.json_last_error().')');
        }

        if (array_key_exists('packages', $result) && is_array($result['packages'])) {
            return $result['packages'];
        }

        // No package list in JSON structure
        throw new ParseException($errorMessage);
    }

    /**
     * Parse the composer.lock file to get the currently used versions of the kunstmaan bundles.
     *
     * @return array
     * @throws Exception\ParseException
     */
    protected function parseComposer()
    {
        $bundles = array();
        foreach ($this->getPackages() as $package) {
            if (!strncmp($package['name'], 'kunstmaan/', strlen('kunstmaan/'))) {
                $bundles[] = array(
                    'name' => $package['name'],
                    'version' => $package['version'],
                    'reference' => $package['source']['reference']
                );
            }
        }

        return $bundles;
    }
}
