<?php

namespace Kunstmaan\AdminBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class DomainConfiguration
 *
 * Default (single domain) configuration handling
 *
 * @package Kunstmaan\AdminBundle\Helper
 */
class DomainConfiguration implements DomainConfigurationInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var bool */
    protected $multiLanguage;

    /** @var array */
    protected $requiredLocales;

    /** @var string */
    protected $defaultLocale;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->multiLanguage = $this->container->getParameter(
            'multilanguage'
        );
        $this->defaultLocale = $this->container->getParameter(
            'defaultlocale'
        );
        $this->requiredLocales = explode(
            '|',
            $this->container->getParameter('requiredlocales')
        );
    }

    /**
     * @return string
     */
    public function getHost()
    {
        $request = $this->getMasterRequest();
        $host = is_null($request) ? '' : $request->getHost();

        return $host;
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        return array($this->getHost());
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param string|null $host
     *
     * @return bool
     */
    public function isMultiLanguage($host = null)
    {
        return $this->multiLanguage;
    }

    /**
     * @param string|null $host
     *
     * @return array
     */
    public function getFrontendLocales($host = null)
    {
        return $this->requiredLocales;
    }

    /**
     * @param string|null $host
     *
     * @return array
     */
    public function getBackendLocales($host = null)
    {
        return $this->requiredLocales;
    }

    /**
     * @return bool
     */
    public function isMultiDomainHost()
    {
        return false;
    }

    /**
     * @param string|null $host
     *
     * @return null
     */
    public function getRootNode($host = null)
    {
        return null;
    }

    /**
     * @return array
     */
    public function getExtraData()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getLocalesExtraData()
    {
        return array();
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getMasterRequest()
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');

        return $requestStack->getMasterRequest();
    }

    /**
     * @return array
     */
    public function getFullHostConfig()
    {
        return array();
    }

    /**
     * @param string|null $host
     *
     * @return null
     */
    public function getFullHost($host = null)
    {
        return null;
    }

    /**
     * @param int $id
     *
     * @return null
     */
    public function getFullHostById($id)
    {
        return null;
    }

    /**
     * @return null
     */
    public function getHostSwitched()
    {
        return null;
    }

    /**
     * @param string|null $host
     *
     * @return null
     */
    public function getHostBaseUrl($host = null)
    {
        return null;
    }
}
