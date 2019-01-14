<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;

/**
 * LocaleSwitcherTwigExtension
 */
class LocaleSwitcherTwigExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('localeswitcher_widget', array($this, 'renderWidget'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new \Twig_SimpleFunction('get_locales', array($this, 'getLocales')),
            new \Twig_SimpleFunction('get_backend_locales', array($this, 'getBackendLocales')),
        );
    }

    /**
     * Render locale switcher widget.
     *
     * @param \Twig_Environment $env
     * @param array             $locales    The locales
     * @param string            $route      The route
     * @param array             $parameters The route parameters
     *
     * @return string
     */
    public function renderWidget(\Twig_Environment $env, $locales, $route, array $parameters = array())
    {
        $template = $env->loadTemplate(
            'KunstmaanAdminBundle:LocaleSwitcherTwigExtension:widget.html.twig'
        );

        return $template->render(
            array_merge(
                $parameters,
                array(
                    'locales' => $locales,
                    'route' => $route,
                )
            )
        );
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->domainConfiguration->getFrontendLocales();
    }

    /**
     * @return array
     */
    public function getBackendLocales($switchedHost = null)
    {
        return $this->domainConfiguration->getBackendLocales($switchedHost);
    }
}
