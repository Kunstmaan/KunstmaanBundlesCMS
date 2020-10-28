<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * LocaleSwitcherTwigExtension
 *
 * @final since 5.4
 */
class LocaleSwitcherTwigExtension extends AbstractExtension
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
            new TwigFunction('localeswitcher_widget', array($this, 'renderWidget'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new TwigFunction('localeswitcher_widget_next', array($this, 'renderWidgetNext'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new TwigFunction('get_locales', array($this, 'getLocales')),
            new TwigFunction('get_backend_locales', array($this, 'getBackendLocales')),
        );
    }

    /**
     * Render locale switcher widget.
     *
     * @param Environment $env
     * @param array       $locales    The locales
     * @param string      $route      The route
     * @param array       $parameters The route parameters
     *
     * @return string
     */
    public function renderWidget(Environment $env, $locales, $route, array $parameters = array())
    {
        $template = $env->load(
            '@KunstmaanAdmin/LocaleSwitcherTwigExtension/widget.html.twig'
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

    public function renderWidgetNext(Environment $env, $locales, $route, array $parameters = array())
    {
        $template = $env->load(
            '@KunstmaanAdmin/LocaleSwitcherTwigExtension/widget_next.html.twig'
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
