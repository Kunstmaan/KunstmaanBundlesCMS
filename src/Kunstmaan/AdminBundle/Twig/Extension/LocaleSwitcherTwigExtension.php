<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

class LocaleSwitcherTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'localeswitcher_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
        );
    }

    public function renderWidget($localeswitcher, $route, array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanAdminBundle:LocaleSwitcherTwigExtension:widget.html.twig");
        $locales=array();
        $help=strtok($localeswitcher, "|");
        while ($help !== false) {
            $locales[] = $help;
            $help = strtok("|");
        }

        return $template->render(array_merge($parameters, array(
            'locales'   => $locales,
            'route'		=> $route
        )));
    }

    public function getName()
    {
        return 'localeswitcher_twig_extension';
    }
}
