<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class MultiDomainAdminTwigExtension extends AbstractExtension
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
        return [
            new TwigFunction('multidomain_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('is_multidomain_site', [$this, 'isMultiDomainSite']),
            new TwigFunction('get_switched_host', [$this, 'getSwitchedHost']),
            new TwigFunction('switched_host_is_current', [$this, 'switchedHostIsCurrent']),
        ];
    }

    /**
     * Render multidomain switcher widget.
     *
     * @param string $route      The route
     * @param array  $parameters The route parameters
     *
     * @return string
     */
    public function renderWidget(Environment $env, $route, array $parameters = [])
    {
        $template = $env->load(
            '@KunstmaanAdmin/MultiDomainAdminTwigExtension/widget.html.twig'
        );

        return $template->render(
            \array_merge(
                $parameters, [
                    'hosts' => $this->getAdminDomainHosts(),
                    'route' => $route,
                ]
            )
        );
    }

    /**
     * Check if site is multiDomain
     *
     * @return bool
     */
    public function isMultiDomainSite()
    {
        return $this->domainConfiguration->isMultiDomainHost();
    }

    /**
     * @return array|null
     */
    public function getSwitchedHost()
    {
        return $this->domainConfiguration->getHostSwitched();
    }

    /**
     * @return bool
     */
    public function switchedHostIsCurrent()
    {
        $hostInfo = $this->getSwitchedHost();

        return $this->domainConfiguration->getHost() === $hostInfo['host'];
    }

    /**
     * @return array
     */
    public function getAdminDomainHosts()
    {
        return $this->domainConfiguration->getHosts();
    }
}
