<?php

namespace Kunstmaan\AdminBundle\Toolbar;

use Doctrine\Common\Cache\Cache;
use Kunstmaan\AdminBundle\Helper\Toolbar\AbstractDataCollector;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BundleVersionDataCollector extends AbstractDataCollector
{
    /** @var VersionChecker */
    private $versionChecker;

    /** @var Logger */
    private $logger;

    /** @var Cache */
    private $cache;

    public function __construct(VersionChecker $versionChecker, Logger $logger, Cache $cache)
    {
        $this->versionChecker = $versionChecker;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    public function getAccessRoles()
    {
        return ['ROLE_SUPER_ADMIN'];
    }

    public function collectData()
    {
        $this->versionChecker->periodicallyCheck();

        $data = $this->cache->fetch('version_check');

        return [
            'data' => $data
        ];
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (!$this->isEnabled()) {
            $this->data = false;
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
}