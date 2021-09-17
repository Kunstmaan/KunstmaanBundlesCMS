<?php

namespace Kunstmaan\AdminBundle\Toolbar;

use Doctrine\Common\Cache\CacheProvider;
use Kunstmaan\AdminBundle\Helper\Toolbar\AbstractDataCollector;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BundleVersionDataCollector extends AbstractDataCollector
{
    /** @var VersionChecker */
    private $versionChecker;

    /** @var AdapterInterface */
    private $cache;

    /**
     * @param CacheProvider|AdapterInterface $cache
     */
    public function __construct(VersionChecker $versionChecker, /*Logger $logger,*/ /* AdapterInterface */ $cache)
    {
        $this->versionChecker = $versionChecker;

        if (!$cache instanceof CacheProvider && !$cache instanceof AdapterInterface) {
            // NEXT_MAJOR Add AdapterInterface typehint for the $cache parameter
            throw new \InvalidArgumentException(sprintf('The "$cache" parameter should extend from "%s" or implement "%s"', CacheProvider::class, AdapterInterface::class));
        }

        $this->cache = $cache;
        if (\func_num_args() > 2) {
            @trigger_error(sprintf('Passing the "logger" service as the second argument in "%s" is deprecated in KunstmaanAdminBundle 5.1 and will be removed in KunstmaanAdminBundle 6.0. Remove the "logger" argument from your service definition.', __METHOD__), E_USER_DEPRECATED);

            $this->cache = func_get_arg(2);
        }

        if ($this->cache instanceof CacheProvider) {
            @trigger_error(sprintf('Passing an instance of "%s" as the second argument in "%s" is deprecated since KunstmaanAdminBundle 5.7 and an instance of "%s" will be required in KunstmaanAdminBundle 6.0.', CacheProvider::class, __METHOD__, AdapterInterface::class), E_USER_DEPRECATED);

            $this->cache = new DoctrineAdapter($cache);
        }
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

    public function collect(Request $request, Response $response, \Exception $exception = null)
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
