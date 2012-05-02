<?php

namespace Kunstmaan\AdminListBundle\Twig;

class AdminListTwigExtension extends \Twig_Extension {

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
            'adminlist_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
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
     * @param FormView        $view      The view to render
     * @param array           $variables Additional variables passed to the template
     *
     * @return string The html markup
     */
    public function renderWidget( $view, $basepath, array $urlparams = array(), array $addparams = array(), array $queryparams = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanAdminListBundle:AdminListTwigExtension:widget.html.twig");
        return $template->render(array(
            'pagination' => $view->getPaginationBean(),
            'filter' =>$view->getAdminListFilter(),
            'basepath' => $basepath,
			'addparams' => $addparams,
            'extraparams' => $urlparams,
        	'queryparams' => $queryparams,
            'adminlist' => $view
        ));
    }

    public function getName()
    {
        return 'adminlist_twig_extension';
    }

}

