<?php

namespace Kunstmaan\AdminListBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;

class AdminListTwigExtension extends \Twig_Extension {

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    protected $container;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            'adminlist_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
            'my_router_params' => new \Twig_Function_Method($this, 'routerParams')
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
     * @param FormView $view The view to render
     * @param string $basepath
     * @param array $urlparams
     * @param array $addparams
     * @param array $queryparams
     * @return string The html markup
     */
    public function renderWidget( FormView $view, $basepath, array $urlparams = array(), array $addparams = array(), array $queryparams = array())
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

    /**
     * Emulating the symfony 2.1.x $request->attributes->get('_route_params') feature.
     * Code based on PagerfantaBundle's twig extension.
     */
    public function routerParams()
    {
        $router = $this->container->get('router');
        $request = $this->container->get('request');

        $routeName = $request->attributes->get('_route');
        $routeParams = $request->query->all();
        foreach ($router->getRouteCollection()->get($routeName)->compile()->getVariables() as $variable) {
            $routeParams[$variable] = $request->attributes->get($variable);
        }

        return $routeParams;
    }

    public function getName()
    {
        return 'adminlist_twig_extension';
    }

}

