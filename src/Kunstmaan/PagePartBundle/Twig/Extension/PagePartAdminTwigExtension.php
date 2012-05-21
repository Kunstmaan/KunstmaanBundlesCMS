<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

class PagePartAdminTwigExtension extends \Twig_Extension
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

    public function __construct()
    {
    }

    public function getFunctions() {
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
     * @param FormView $view      The view to render
     * @param array    $variables Additional variables passed to the template
     *
     * @return string The html markup
     */
    public function renderWidget( $view , $form , array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanPagePartBundle:PagePartAdminTwigExtension:widget.html.twig");

        return $template->render(array_merge($parameters, array(
            'pagepartadmin' => $view,
            'form' => $form
        )));
    }

    public function getName()
    {
        return 'pagepartadmin_twig_extension';
    }
}
