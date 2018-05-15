<?php

namespace Kunstmaan\AdminListBundle\Twig;

use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\Service\ExportService;
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
        return [
            new Twig_SimpleFunction('adminlist_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('adminthumb_widget', [$this, 'renderThumbWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('supported_export_extensions', [$this, 'getSupportedExtensions']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('instanceof', [$this, 'isInstanceOf']),
        ];
    }

    /**
     * @param object $object
     * @param string $class
     *
     * @return bool
     */
    public function isInstanceOf($object, $class)
    {
        return $object instanceof $class;
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
     * @param AdminList        $view      The view to render
     * @param string           $basepath  The base path
     * @param array            $urlparams Additional url params
     * @param array            $addparams Add params
     *
     * @return string The html markup
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderWidget(Twig_Environment $env, AdminList $view, $basepath, array $urlparams = [], array $addparams = [])
    {
        $filterBuilder = $view->getFilterBuilder();

        return $env->render(
            'KunstmaanAdminListBundle:AdminListTwigExtension:widget.html.twig',
            [
                'filter' => $filterBuilder,
                'basepath' => $basepath,
                'addparams' => $addparams,
                'extraparams' => $urlparams,
                'adminlist' => $view,
            ]
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
     * @param AdminList        $view      The view to render
     * @param string           $basepath  The base path
     * @param array            $urlparams Additional url params
     * @param array            $addparams Add params
     *
     * @return string The html markup
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderThumbWidget(Twig_Environment $env, AdminList $view, $basepath, array $urlparams = [], array $addparams = [])
    {
        $filterBuilder = $view->getFilterBuilder();

        return $env->render(
            'KunstmaanAdminListBundle:AdminListTwigExtension:thumbwidget.html.twig',
            [
                'filter' => $filterBuilder,
                'basepath' => $basepath,
                'addparams' => $addparams,
                'extraparams' => $urlparams,
                'adminlist' => $view,
            ]
        );
    }

    /**
     * @return array
     */
    public function getSupportedExtensions()
    {
        return ExportService::getSupportedExtensions();
    }
}
