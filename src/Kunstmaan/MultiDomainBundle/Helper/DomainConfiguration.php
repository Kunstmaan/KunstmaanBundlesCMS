<?php

namespace Kunstmaan\MultiDomainBundle\Helper;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\AdminBundle\Helper\DomainConfiguration as BaseDomainConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DomainConfiguration extends BaseDomainConfiguration
{
    const OVERRIDE_HOST = '_override_host';
    const SWITCH_HOST = '_switch_host';

    /**
     * @var Node
     */
    protected $rootNode = null;

    /**
     * @var array
     */
    protected $hosts;

    /**
     * @var array
     */
    protected $aliases = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->hosts = $container->getParameter('kunstmaan_multi_domain.hosts');
        foreach ($this->hosts as $host => $hostInfo) {
            if (isset($hostInfo['aliases'])) {
                foreach ($hostInfo['aliases'] as $alias) {
                    $this->aliases[$alias] = $host;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getHost()
    {
        if ($this->hasHostOverride()) {
            return $this->getHostOverride();
        }

        $host = parent::getHost();
        if (isset($this->aliases[$host])) {
            $host = $this->aliases[$host];
        }

        return $host;
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        return array_keys($this->hosts);
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        $host = $this->getHost();
        if (isset($this->hosts[$host]['default_locale'])) {
            return $this->hosts[$host]['default_locale'];
        }

        return parent::getDefaultLocale();
    }

    /**
     * @param string|null $host
     *
     * @return bool
     */
    public function isMultiLanguage($host = null)
    {
        $host = $this->getRealHost($host);

        if (isset($this->hosts[$host])) {
            $hostInfo = $this->hosts[$host];

            return ('multi_lang' === $hostInfo['type']);
        }

        return parent::isMultiLanguage();
    }

    /**
     * @param string|null $host
     *
     * @return array
     */
    public function getFrontendLocales($host = null)
    {
        $host = $this->getRealHost($host);

        if (isset($this->hosts[$host]['locales'])) {
            return array_keys($this->hosts[$host]['locales']);
        }

        return parent::getBackendLocales();
    }

    /**
     * @param string|null $host
     *
     * @return array
     */
    public function getBackendLocales($host = null)
    {
        $host = $this->getRealHost($host);

        if (isset($this->hosts[$host]['locales'])) {
            return array_values($this->hosts[$host]['locales']);
        }

        return parent::getBackendLocales();
    }

    /**
     * @return bool
     */
    public function isMultiDomainHost()
    {
        $host = $this->getHost();

        return isset($this->hosts[$host]);
    }

    /**
     * Fetch the root node for the current host
     *
     * @param string|null $host
     *
     * @return Node|null
     */
    public function getRootNode($host = null)
    {
        if (!$this->isMultiDomainHost()) {
            return parent::getRootNode();
        }

        if (is_null($this->rootNode)) {
            $host = $this->getRealHost($host);

            $internalName = $this->hosts[$host]['root'];
            $em = $this->container->get('doctrine.orm.entity_manager');
            $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
            $this->rootNode = $nodeRepo->getNodeByInternalName($internalName);
        }

        return $this->rootNode;
    }

    /**
     * Return (optional) extra config settings for the current host
     */
    public function getExtraData()
    {
        $host = $this->getHost();

        if (!isset($this->hosts[$host]['extra'])) {
            return parent::getExtraData();
        }

        return $this->hosts[$host]['extra'];
    }

    /**
     * Return (optional) extra config settings for the locales for the current host
     */
    public function getLocalesExtraData()
    {
        $host = $this->getHost();

        if (!isset($this->hosts[$host]['locales_extra'])) {
            return parent::getLocalesExtraData();
        }

        return $this->hosts[$host]['locales_extra'];
    }

    /**
     * @return bool
     */
    protected function hasHostOverride()
    {
        $request = $this->getMasterRequest();

        return !is_null($request) &&
        $this->isAdminRoute($request->getRequestUri()) &&
        $request->hasPreviousSession() &&
        $request->getSession()->has(self::OVERRIDE_HOST);
    }

    /**
     * @return bool
     */
    public function hasHostSwitched()
    {
        $request = $this->getMasterRequest();

        return !is_null($request) &&
        $this->isAdminRoute($request->getRequestUri()) &&
        $request->hasPreviousSession() &&
        $request->getSession()->has(self::SWITCH_HOST);
    }

    /**
     * @return string|null
     */
    protected function getHostOverride()
    {
        if (null !== ($request = $this->getMasterRequest()) && $request->hasPreviousSession()) {
            return $request->getSession()->get(self::OVERRIDE_HOST);
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getHostSwitched()
    {
        $request = $this->getMasterRequest();

        $host = $this->getHost();

        if ($this->hasHostSwitched()) {
            $host = $request->getSession()->get(self::SWITCH_HOST);
        }

        return $this->hosts[$host];
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    protected function isAdminRoute($url)
    {
        preg_match(
            '/^\/(app_(.*)\.php\/)?([a-zA-Z_-]{2,5}\/)?admin\/(.*)/',
            $url,
            $matches
        );

        // Check if path is part of admin area
        if (count($matches) === 0) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getFullHostConfig()
    {
        return $this->hosts;
    }

    /**
     * @param string|null $host
     *
     * @return array
     */
    public function getFullHost($host = null)
    {
        $host = $this->getRealHost($host);

        if ($host) {
            return $this->hosts[$host];
        }

        return null;
    }


    /**
     * @param int $id
     *
     * @return array
     */
    public function getFullHostById($id)
    {
        foreach ($this->hosts as $host => $parameters) {
            if (!isset($parameters['id']) || $parameters['id'] !== $id) {
                continue;
            }

            return $parameters;
        }

        return null;
    }

    /**
     * @param string|null $host
     *
     * @return string
     */
    public function getHostBaseUrl($host = null)
    {
        $config = $this->getFullHost($host);

        return sprintf('%s://%s', $config['protocol'], $config['host']);
    }

    /**
     * @param string|null $host
     *
     * @return null|string
     */
    private function getRealHost($host = null)
    {
        if (!$host) {
            $host = $this->getHost();
        }

        return $host;
    }
}
