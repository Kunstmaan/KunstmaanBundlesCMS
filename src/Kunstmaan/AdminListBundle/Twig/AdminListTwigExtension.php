<?php

namespace Kunstmaan\AdminListBundle\Twig;

use Kunstmaan\AdminListBundle\Service\ExportService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\AdminListBundle\AdminList\AdminList;

/**
 * AdminListTwigExtension.
 */
class AdminListTwigExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('adminlist_widget', array($this, 'renderWidget'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new \Twig_SimpleFunction('supported_export_extensions', array($this, 'getSupportedExtensions')),
        );
    }

    /**
     * Renders the HTML for a given view.
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
     * @param \Twig_Environment $env
     * @param AdminList         $view      The view to render
     * @param string            $basepath  The base path
     * @param array             $urlparams Additional url params
     * @param array             $addparams Add params
     *
     * @return string The html markup
     */
    public function renderWidget(\Twig_Environment $env, AdminList $view, $basepath, array $urlparams = array(), array $addparams = array())
    {
        $template = $env->loadTemplate('KunstmaanAdminListBundle:AdminListTwigExtension:widget.html.twig');

        $filterBuilder = $view->getFilterBuilder();

        return $template->render(array(
            'filter' => $filterBuilder,
            'basepath' => $basepath,
            'addparams' => $addparams,
            'extraparams' => $urlparams,
            'adminlist' => $view,
        ));
    }

    public function getSupportedExtensions()
    {
        return ExportService::getSupportedExtensions();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'adminlist_twig_extension';
    }
}
