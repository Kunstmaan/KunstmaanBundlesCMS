<?php

namespace Kunstmaan\NodeBundle\Helper;

use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class DomainConfiguration
 *
 * Default (single domain) configuration handling
 *
 * @package Kunstmaan\NodeBundle\Helper
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

    /** @var Node */
    protected $rootNode = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container       = $container;
        $this->multiLanguage   = $this->container->getParameter(
            'multilanguage'
        );
        $this->defaultLocale   = $this->container->getParameter(
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
        $host    = is_null($request) ? '' : $request->getHost();

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
     * @param string $locale
     *
     * @return string
     */
    public function getTranslationHost($locale)
    {
        return $this->getHost();
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @return bool
     */
    public function isMultiLanguage()
    {
        return $this->multiLanguage;
    }

    /**
     * @return array
     */
    public function getFrontendLocales()
    {
        return $this->requiredLocales;
    }

    /**
     * @return array
     */
    public function getBackendLocales()
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
     * @return Node
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }

    /**
     * @return array
     */
    public function getExtraData()
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
}
