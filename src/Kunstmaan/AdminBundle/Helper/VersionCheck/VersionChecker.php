<?php

namespace Kunstmaan\AdminBundle\Helper\VersionCheck;

use Doctrine\Common\Cache\Cache;
use Exception;
use GuzzleHttp\Client;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Version checker
 */
class VersionChecker
{
    /** @var ContainerInterface */
    private $container;

    /** @var RequestStack */
    private $requestStack;

    /** @var Cache */
    private $cache;

    /** @var TranslatorInterface */
    private $translator;

    /** @var string */
    private $rootDir;

    /** @var string */
    private $webserviceUrl;

    /** @var int */
    private $cacheTimeframe;

    /** @var bool */
    private $enabled;

    /** @var string */
    private $websiteTitle;

    /**
     * VersionChecker constructor.
     *
     * @param ContainerInterface|RequestStack $requestStack
     * @param Cache                           $cache
     * @param TranslatorInterface|null        $translator
     * @param string|null                     $rootDir
     * @param string|null                     $webserviceUrl
     * @param int|null                        $cacheTimeframe
     * @param bool|null                       $enabled
     * @param string|null                     $websiteTitle
     */
    public function __construct(
        /** RequestStack */ $requestStack,
        Cache $cache,
        TranslatorInterface $translator = null,
        $rootDir = null,
        $webserviceUrl = null,
        $cacheTimeframe = null,
        $enabled = null,
        $websiteTitle = null
    ) {

        $this->cache = $cache;

        if ($requestStack instanceof ContainerInterface) {
            @trigger_error(
                'Container injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
                E_USER_DEPRECATED
            );

            $this->container = $requestStack;
            $this->requestStack = $requestStack->get('request_stack');
            $this->translator = $requestStack->get(Translator::class);
            $this->rootDir = $requestStack->getParameter('kernel.root_dir');
            $this->webserviceUrl = $requestStack->getParameter('version_checker.url');
            $this->cacheTimeframe = $requestStack->getParameter('version_checker.timeframe');
            $this->enabled = $requestStack->getParameter('version_checker.enabled');
            $this->websiteTitle = $requestStack->getParameter('websitetitle');

            return;
        }

        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->rootDir = $rootDir;
        $this->webserviceUrl = $webserviceUrl;
        $this->cacheTimeframe = $cacheTimeframe;
        $this->enabled = $enabled;
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

        $data = $this->cache->fetch('version_check');
        if (!\is_array($data)) {
            $this->check();
        }
    }

    /**
     * @return bool|mixed|void
     * @throws ParseException
     */
    public function check()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $jsonData = json_encode(
            [
                'host' => $this->requestStack->getCurrentRequest()->getHttpHost(),
                'installed' => filectime($this->rootDir.'/../bin/console'),
                'bundles' => $this->parseComposer(),
                'project' => $this->websiteTitle,
            ]
        );

        try {
            $client = new Client(['connect_timeout' => 3, 'timeout' => 1]);
            $response = $client->post($this->webserviceUrl, ['body' => $jsonData]);
            $data = json_decode($response->getBody()->getContents());

            if (null === $data) {
                return false;
            }

            // Save the result in the cache to make sure we don't do the check too often
            $this->cache->save('version_check', $data, $this->cacheTimeframe);

            return $data;
        } catch (Exception $e) {
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
        $rootPath = \dirname($this->rootDir);

        return $rootPath.'/composer.lock';
    }

    /**
     * Returns a list of composer packages.
     *
     * @return array
     * @throws ParseException
     */
    protected function getPackages()
    {
        $errorMessage = $this->translator->trans('settings.version.error_parsing_composer');

        $composerPath = $this->getLockPath();
        if (!file_exists($composerPath)) {
            throw new ParseException(
                $this->translator->trans('settings.version.composer_lock_not_found')
            );
        }

        $json = file_get_contents($composerPath);
        $result = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseException($errorMessage.' (#'.json_last_error().')');
        }

        if (array_key_exists('packages', $result) && \is_array($result['packages'])) {
            return $result['packages'];
        }

        // No package list in JSON structure
        throw new ParseException($errorMessage);
    }

    /**
     * Parse the composer.lock file to get the currently used versions of the kunstmaan bundles.
     *
     * @return array
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
