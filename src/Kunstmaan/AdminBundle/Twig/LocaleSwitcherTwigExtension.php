<?php

namespace Kunstmaan\AdminBundle\Twig;

/**
 * LocaleSwitcherTwigExtension
 */
class LocaleSwitcherTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * Initializes the runtime environment.
     *
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('localeswitcher_widget', array($this, 'renderWidget'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Render locale switcher widget.
     *
     * @param array  $locales    The locales
     * @param string $route      The route
     * @param array  $parameters The route parameters
     *
     * @return string
     */
    public function renderWidget($locales, $route, array $parameters = array())
    {
        $template = $this->environment->loadTemplate(
            "KunstmaanAdminBundle:LocaleSwitcherTwigExtension:widget.html.twig"
        );

        return $template->render(
            array_merge(
                $parameters,
                array(
                    'locales' => $locales,
                    'route'   => $route
                )
            )
        );
    }

    /**
     * Get the Twig extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'localeswitcher_twig_extension';
    }
}
