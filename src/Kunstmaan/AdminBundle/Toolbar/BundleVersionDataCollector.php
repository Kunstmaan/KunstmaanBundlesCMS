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

    /** @var Cache */
    private $cache;

    public function __construct(VersionChecker $versionChecker, /*Logger $logger,*/ /* Cache */ $cache)
    {
        $this->versionChecker = $versionChecker;

        if (func_num_args() > 2) {
            @trigger_error(sprintf('Passing the "logger" service as the second argument in "%s" is deprecated in KunstmaanAdminBundle 5.1 and will be removed in KunstmaanAdminBundle 6.0. Remove the "logger" argument from your service definition.', __METHOD__), E_USER_DEPRECATED);

            $this->cache = func_get_arg(2);

            return;
        }

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
            'data' => $data,
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
