<?php

namespace Kunstmaan\CacheBundle\Helper;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCacheBundle\CacheManager;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;

/**
 * Class VarnishHelper.
 */
class VarnishHelper
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * VarnishHelper constructor.
     *
     * @param CacheManager                 $cacheManager
     * @param DomainConfigurationInterface $domainConfiguration
     */
    public function __construct(CacheManager $cacheManager, DomainConfigurationInterface $domainConfiguration)
    {
        $this->cacheManager = $cacheManager;
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * @param string $path
     * @param bool   $allDomains
     *
     * @return CacheInvalidator
     */
    public function banPath($path, $allDomains = false)
    {
        $hosts = $this->getHosts($allDomains);

        return $this->cacheManager->invalidateRegex($path, null, $hosts);
    }

    protected function getHosts($allDomains = false)
    {
        if ($allDomains) {
            return $this->domainConfiguration->getHosts();
        }

        return $this->domainConfiguration->getHost();
    }
}
