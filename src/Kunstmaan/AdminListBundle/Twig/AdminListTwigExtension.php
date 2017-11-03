<?php

namespace Kunstmaan\AdminListBundle\Twig;

use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\Service\ExportService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * AdminListTwigExtension
 */
class AdminListTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('adminlist_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('adminthumb_widget', [$this, 'renderThumbWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('supported_export_extensions', [$this, 'getSupportedExtensions']),
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
     * @param Twig_Environment $env
     * @param AdminList         $view      The view to render
     * @param string            $basepath  The base path
     * @param array             $urlparams Additional url params
     * @param array             $addparams Add params
     *
     * @return string The html markup
     */
    public function renderWidget(Twig_Environment $env, AdminList $view, $basepath, array $urlparams = [], array $addparams = [])
    {
        $template = $env->loadTemplate("KunstmaanAdminListBundle:AdminListTwigExtension:widget.html.twig");

        $filterBuilder = $view->getFilterBuilder();

        return $template->render(array(
            'filter' => $filterBuilder,
            'basepath' => $basepath,
            'addparams' => $addparams,
            'extraparams' => $urlparams,
            'adminlist' => $view
        ));
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
     * @param Twig_Environment $env
     * @param AdminList         $view      The view to render
     * @param string            $basepath  The base path
     * @param array             $urlparams Additional url params
     * @param array             $addparams Add params
     *
     * @return string The html markup
     */
    public function renderThumbWidget(Twig_Environment $env, AdminList $view, $basepath, array $urlparams = [], array $addparams = [])
    {
        $template = $env->loadTemplate("KunstmaanAdminListBundle:AdminListTwigExtension:thumbwidget.html.twig");

        $filterBuilder = $view->getFilterBuilder();

        return $template->render(array(
            'filter' => $filterBuilder,
            'basepath' => $basepath,
            'addparams' => $addparams,
            'extraparams' => $urlparams,
            'adminlist' => $view
        ));
    }

    /**
     * @return array
     */
    public function getSupportedExtensions()
    {
        return ExportService::getSupportedExtensions();
    }
}
