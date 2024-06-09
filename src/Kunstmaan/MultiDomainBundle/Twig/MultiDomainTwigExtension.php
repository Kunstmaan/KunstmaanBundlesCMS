<?php

namespace Kunstmaan\MultiDomainBundle\Twig;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MultiDomainTwigExtension extends AbstractExtension
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
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_multi_domain_hosts', [$this, 'getMultiDomainHosts']),
            new TwigFunction('get_current_host', [$this, 'getCurrentHost']),
            new TwigFunction('get_extra_data', [$this, 'getExtraData']),
            new TwigFunction('get_current_full_host', [$this, 'getCurrentFullHost']),
        ];
    }

    public function getExtraData($key)
    {
        $extraData = $this->domainConfiguration->getExtraData();

        if ($extraData[$key]) {
            return $extraData[$key];
        }

        return null;
    }

    public function getMultiDomainHosts(): array
    {
        return $this->domainConfiguration->getHosts();
    }

    public function getCurrentHost(): string
    {
        return $this->domainConfiguration->getHost();
    }

    public function getCurrentFullHost(): ?array
    {
        return $this->domainConfiguration->getFullHost();
    }
}
