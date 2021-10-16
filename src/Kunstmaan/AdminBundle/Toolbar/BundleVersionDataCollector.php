<?php

namespace Kunstmaan\AdminBundle\Toolbar;

use Kunstmaan\AdminBundle\Helper\Toolbar\AbstractDataCollector;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BundleVersionDataCollector extends AbstractDataCollector
{
    /** @var VersionChecker */
    private $versionChecker;

    /** @var AdapterInterface */
    private $cache;

    public function __construct(VersionChecker $versionChecker, AdapterInterface $cache)
    {
        $this->versionChecker = $versionChecker;
        $this->cache = $cache;
    }

    public function getAccessRoles()
    {
        return ['ROLE_SUPER_ADMIN'];
    }

    public function collectData()
    {
        $this->versionChecker->periodicallyCheck();

        $cacheItem = $this->cache->getItem(VersionChecker::CACHE_KEY);
        $collectorData = [];
        if ($cacheItem->isHit()) {
            $collectorData = $cacheItem->get() ?? [];
        }

        return [
            'data' => $collectorData,
        ];
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        if (!$this->isEnabled()) {
            $this->data = [];
        } else {
            $this->data = $this->collectData();
        }
    }

    /**
     * Gets the data for template
     *
     * @return array The request events
     */
    public function getTemplateData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kuma_bundle_versions';
    }

    public function isEnabled()
    {
        return $this->versionChecker->isEnabled();
    }

    public function reset()
    {
        $this->data = [];
    }
}
