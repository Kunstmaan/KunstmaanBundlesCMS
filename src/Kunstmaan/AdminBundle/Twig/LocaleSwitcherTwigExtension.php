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
            'localeswitcher_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
        );
    }

    /**
     * Render locale switcher widget.
     *
     * @param string $localeSwitcher The locale switcher
     * @param string $route          The route
     * @param array  $parameters     The route parameters
     *
     * @return string
     */
    public function renderWidget($localeSwitcher, $route, array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanAdminBundle:LocaleSwitcherTwigExtension:widget.html.twig");
        $locales=array();
        $help=strtok($localeSwitcher, "|");
        while ($help !== false) {
            $locales[] = $help;
            $help = strtok("|");
        }

        return $template->render(array_merge($parameters, array(
            'locales'   => $locales,
            'route'		=> $route
        )));
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
