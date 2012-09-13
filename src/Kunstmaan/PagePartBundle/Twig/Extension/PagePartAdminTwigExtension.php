<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Symfony\Component\Form\FormView;

class PagePartAdminTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'pagepartadmin_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
        );
    }

    /**
     * Renders the HTML for a given view
     *
     * Example usage in Twig:
     *
     *     {{ form_widget(view) }}
     *
     * You can pass options during the call:
     *
     *     {{ form_widget(view, {'attr': {'class': 'foo'}}) }}
     *
     *     {{ form_widget(view, {'separator': '+++++'}) }}
     *
     * @param \Symfony\Component\Form\FormView $view       The view to render
     * @param null                             $form
     * @param array                            $parameters Additional variables passed to the template
     *
     * @return string The html markup
     */
    public function renderWidget(FormView $view , $form = null , array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanPagePartBundle:PagePartAdminTwigExtension:widget.html.twig");

        return $template->render(array_merge($parameters, array(
            'pagepartadmin' => $view,
            'form' => $form
        )));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pagepartadmin_twig_extension';
    }
}
