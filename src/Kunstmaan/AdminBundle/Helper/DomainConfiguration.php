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

    /** @var RequestStack */
    protected $requestStack;

    /** @var array */
    protected $requiredLocales;

    /** @var string */
    protected $defaultLocale;

    /**
     * DomainConfiguration constructor.
     *
     * @param bool|ContainerInterface $multiLanguage
     * @param RequestStack|null       $requestStack
     * @param string|null             $defaultLocale
     * @param string|null             $requiredLocales
     */
    public function __construct($multiLanguage, /* RequestStack */ $requestStack = null, $defaultLocale = null, $requiredLocales = null)
    {
        if ($multiLanguage instanceof ContainerInterface) {
            @trigger_error(
                'Container injection is deprecated in KunstmaanAdminBundle 5.1 and will be removed in KunstmaanAdminBundle 6.0.',
                E_USER_DEPRECATED
            );

            $this->container = $multiLanguage;
            $this->multiLanguage = $multiLanguage->getParameter('multilanguage');
            $this->requestStack = $multiLanguage->get('request_stack');
            $this->defaultLocale = $multiLanguage->getParameter('defaultlocale');
            $this->requiredLocales = explode('|', $multiLanguage->getParameter('requiredlocales'));

            return;
        }

        $this->multiLanguage = $multiLanguage;
        $this->defaultLocale = $defaultLocale;
        $this->requiredLocales = $requiredLocales;
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        $request = $this->getMasterRequest();

        return null === $request ? '' : $request->getHost();
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        return [$this->getHost()];
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
        return [];
    }

    /**
     * @return array
     */
    public function getLocalesExtraData()
    {
        return [];
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getMasterRequest()
    {
        return $this->requestStack->getMasterRequest();
    }

    /**
     * @return array
     */
    public function getFullHostConfig()
    {
        return [];
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
