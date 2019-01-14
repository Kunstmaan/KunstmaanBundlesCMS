<?php

namespace Kunstmaan\MultiDomainBundle\Twig;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;

class MultiDomainTwigExtension extends \Twig_Extension
{
    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    public function __construct(DomainConfigurationInterface $domainConfiguration)
    {
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_multi_domain_hosts', array($this, 'getMultiDomainHosts')),
            new \Twig_SimpleFunction('get_current_host', array($this, 'getCurrentHost')),
            new \Twig_SimpleFunction('get_extra_data', array($this, 'getExtraData')),
            new \Twig_SimpleFunction('get_current_full_host', array($this, 'getCurrentFullHost')),
        );
    }

    /**
     * @param $key
     */
    public function getExtraData($key)
    {
        $extraData = $this->domainConfiguration->getExtraData();

        if ($extraData[$key]) {
            return $extraData[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getMultiDomainHosts()
    {
        return $this->domainConfiguration->getHosts();
    }

    /**
     * @return string
     */
    public function getCurrentHost()
    {
        return $this->domainConfiguration->getHost();
    }

    /**
     * @return array
     */
    public function getCurrentFullHost()
    {
        return $this->domainConfiguration->getFullHost();
    }
}
