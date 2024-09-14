<?php

namespace Kunstmaan\AdminBundle\Helper\VersionCheck;

use GuzzleHttp\Client;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class VersionChecker
{
    public const CACHE_KEY = 'version_check';

    /** @var AdapterInterface */
    private $cache;

    /** @var string */
    private $webserviceUrl;

    /** @var int */
    private $cacheTimeframe;

    /** @var bool */
    private $enabled;

    /** @var Client */
    private $client;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RequestStack */
    private $requestStack;

    /** @var string */
    private $websiteTitle;

    /** @var string */
    private $projectDir;

    public function __construct(
        AdapterInterface $cache,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        string $webserviceUrl,
        int $cacheTimeframe,
        bool $enabled,
        string $projectDir,
        string $websiteTitle,
    ) {
        $this->cache = $cache;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->webserviceUrl = $webserviceUrl;
        $this->cacheTimeframe = $cacheTimeframe;
        $this->enabled = $enabled;
        $this->projectDir = $projectDir;
        $this->websiteTitle = $websiteTitle;
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
     * @throws ParseException
     */
    public function periodicallyCheck()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $cacheItem = $this->cache->getItem(self::CACHE_KEY);
        if (!$cacheItem->isHit() || !\is_array($cacheItem->get())) {
            $this->check();
        }
    }

    /**
     * Get the version details via webservice.
     *
     * @return mixed a list of bundles if available
     *
     * @throws ParseException
     */
    public function check()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $host = $this->requestStack->getCurrentRequest()->getHttpHost();
        $console = realpath($this->projectDir . '/bin/console');
        $installed = filectime($console);
        $bundles = $this->parseComposer();

        $jsonData = json_encode([
            'host' => $host,
            'installed' => $installed,
            'bundles' => $bundles,
            'project' => $this->translator->trans($this->websiteTitle),
        ]);

        try {
            $client = $this->getClient();
            $response = $client->post($this->webserviceUrl, ['body' => $jsonData]);
            $contents = $response->getBody()->getContents();
            $data = json_decode($contents);

            if (null === $data) {
                return false;
            }

            // Save the result in the cache to make sure we don't do the check too often
            $cacheItem = $this->cache->getItem(self::CACHE_KEY);
            $cacheItem->expiresAfter($this->cacheTimeframe);
            $cacheItem->set($data);

            $this->cache->save($cacheItem);

            return $data;
        } catch (\Exception $e) {
            // We did not receive valid json
            return false;
        }
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = new Client(['connect_timeout' => 3, 'timeout' => 1]);
        }

        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Returns the absolute path to the composer.lock file.
     *
     * @return string
     */
    protected function getLockPath()
    {
        return $this->projectDir . '/composer.lock';
    }

    /**
     * Returns a list of composer packages.
     *
     * @return array
     *
     * @throws ParseException
     */
    protected function getPackages()
    {
        $composerPath = $this->getLockPath();
        if (!file_exists($composerPath)) {
            throw new ParseException($this->translator->trans('settings.version.composer_lock_not_found'));
        }

        $json = file_get_contents($composerPath);
        $result = json_decode($json, true);

        $errorMessage = $this->translator->trans('settings.version.error_parsing_composer');
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseException($errorMessage . ' (#' . json_last_error() . ')');
        }

        if (\array_key_exists('packages', $result) && \is_array($result['packages'])) {
            return $result['packages'];
        }

        // No package list in JSON structure
        throw new ParseException($errorMessage);
    }

    /**
     * Parse the composer.lock file to get the currently used versions of the kunstmaan bundles.
     *
     * @return array
     *
     * @throws ParseException
     */
    protected function parseComposer()
    {
        $bundles = [];
        foreach ($this->getPackages() as $package) {
            if (!strncmp($package['name'], 'kunstmaan/', \strlen('kunstmaan/'))) {
                $bundles[] = [
                    'name' => $package['name'],
                    'version' => $package['version'],
                    'reference' => $package['source']['reference'],
                ];
            }
        }

        return $bundles;
    }
}
