<?php

namespace Kunstmaan\MultiDomainBundle\Twig;

use Kunstmaan\NodeBundle\Helper\DomainConfigurationInterface;

class MultiDomainTwigExtension extends \Twig_Extension
{
    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @param DomainConfigurationInterface $domainConfiguration
     */
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
            'get_multi_domain_hosts' => new \Twig_Function_Method(
                $this, 'getMultiDomainHosts'
            ),
            'get_current_host' => new \Twig_Function_Method(
                $this, 'getCurrentHost'
            ),
        );
    }

    /**
     * @return array
     */
    public function getMultiDomainHosts()
    {
        return $this->domainConfiguration->getHosts();
    }

    /**
     * @return array
     */
    public function getCurrentHost()
    {
        return $this->domainConfiguration->getHost();
    }

    /**
     * Get the Twig extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'MultiDomainTwigExtension';
    }
}
