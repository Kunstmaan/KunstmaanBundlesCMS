<?php

namespace Kunstmaan\AdminBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Default (single domain) configuration handling
 */
class DomainConfiguration implements DomainConfigurationInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var RequestStack */
    private $requestStack;

    /** @var bool */
    protected $multiLanguage;

    /** @var array */
    protected $requiredLocales;

    /** @var string */
    protected $defaultLocale;

    /**
     * @param ContainerInterface|string $multilanguage
     */
    public function __construct(/*ContainerInterface|RequestStack*/ $requestStack, $multilanguage = null, $defaultLocale = null, $requiredLocales = null)
    {
        if ($requestStack instanceof ContainerInterface) {
            @trigger_error('Container injection and the usage of the container is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.', E_USER_DEPRECATED);

            $this->container = $requestStack;
            $this->multiLanguage = $this->container->getParameter('kunstmaan_admin.multi_language');
            $this->defaultLocale = $this->container->getParameter('kunstmaan_admin.default_locale');
            $this->requiredLocales = explode('|', $this->container->getParameter('kunstmaan_admin.required_locales'));
            $this->requestStack = $this->container->get('request_stack');

            return;
        }

        $this->requestStack = $requestStack;
        $this->multiLanguage = $multilanguage;
        $this->defaultLocale = $defaultLocale;

        $this->requiredLocales = explode('|', $requiredLocales);
    }

    /**
     * @return string
     */
    public function getHost()
    {
        $request = $this->getMasterRequest();
        $host = \is_null($request) ? '' : $request->getHost();

        return $host;
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
     * @return \Symfony\Component\HttpFoundation\Request|null
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
     */
    public function getFullHost($host = null)
    {
        return null;
    }

    /**
     * @param string|int $id
     */
    public function getFullHostById($id)
    {
        return null;
    }

    public function getHostSwitched()
    {
        return null;
    }

    /**
     * @param string|null $host
     */
    public function getHostBaseUrl($host = null)
    {
        return null;
    }
}
