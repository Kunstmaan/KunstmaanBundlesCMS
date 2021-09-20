<?php

namespace Kunstmaan\AdminBundle\Helper\VersionCheck;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use Exception;
use GuzzleHttp\Client;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class VersionChecker
{
    public const CACHE_KEY = 'version_check';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var AdapterInterface
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
     * @var Client
     */
    private $client;

    /**
     * @var TranslatorInterface|LegacyTranslatorInterface
     */
    private $translator;

    /**
     * @param CacheProvider|AdapterInterface $cache
     */
    public function __construct(ContainerInterface $container, /* AdapterInterface */ $cache, $translator)
    {
        $this->container = $container;

        if (!$cache instanceof CacheProvider && !$cache instanceof AdapterInterface) {
            // NEXT_MAJOR Add AdapterInterface typehint for the $cache parameter
            throw new \InvalidArgumentException(sprintf('The "$cache" parameter should extend from "%s" or implement "%s"', CacheProvider::class, AdapterInterface::class));
        }

        $this->cache = $cache;
        if ($cache instanceof CacheProvider) {
            @trigger_error(sprintf('Passing an instance of "%s" as the second argument in "%s" is deprecated since KunstmaanAdminBundle 5.7 and an instance of "%s" will be required in KunstmaanAdminBundle 6.0.', CacheProvider::class, __METHOD__, AdapterInterface::class), E_USER_DEPRECATED);

            $this->cache = new DoctrineAdapter($cache);
        }

        // NEXT_MAJOR Add "Symfony\Contracts\Translation\TranslatorInterface" typehint when sf <4.4 support is removed.
        if (!$translator instanceof TranslatorInterface && !$translator instanceof LegacyTranslatorInterface) {
            throw new \InvalidArgumentException(sprintf('The "$translator" parameter should be instance of "%s" or "%s"', TranslatorInterface::class, LegacyTranslatorInterface::class));
        }

        $this->translator = $translator;

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

        $host = $this->container->get('request_stack')->getCurrentRequest()->getHttpHost();
        $console = realpath($this->container->get('kernel')->getProjectDir() . '/bin/console');
        $installed = filectime($console);
        $bundles = $this->parseComposer();
        $title = $this->container->getParameter('kunstmaan_admin.website_title');

        $jsonData = json_encode([
            'host' => $host,
            'installed' => $installed,
            'bundles' => $bundles,
            'project' => $this->translator->trans($title),
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
        } catch (Exception $e) {
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
        $kernel = $this->container->get('kernel');
        $rootPath = $kernel->getProjectDir();

        return $rootPath . '/composer.lock';
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
        $translator = $this->container->get('translator');
        $errorMessage = $translator->trans('settings.version.error_parsing_composer');

        $composerPath = $this->getLockPath();
        if (!file_exists($composerPath)) {
            throw new ParseException($translator->trans('settings.version.composer_lock_not_found'));
        }

        $json = file_get_contents($composerPath);
        $result = json_decode($json, true);

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
