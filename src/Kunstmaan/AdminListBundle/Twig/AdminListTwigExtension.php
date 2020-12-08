<?php

namespace Kunstmaan\AdminListBundle\Twig;

use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\Service\ExportService;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * AdminListTwigExtension
 *
 * @final since 5.4
 */
class AdminListTwigExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('adminlist_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('adminthumb_widget', [$this, 'renderThumbWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('supported_export_extensions', [$this, 'getSupportedExtensions']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new TwigTest('instanceof', [$this, 'isInstanceOf']),
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
     * @param AdminList $view      The view to render
     * @param string    $basepath  The base path
     * @param array     $urlparams Additional url params
     * @param array     $addparams Add params
     *
     * @return string The html markup
     *
     * @throws \Throwable
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderWidget(Environment $env, AdminList $view, $basepath, array $urlparams = [], array $addparams = [])
    {
        $filterBuilder = $view->getFilterBuilder();

        return $env->render(
            '@KunstmaanAdminList/AdminListTwigExtension/widget.html.twig',
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
     * @param AdminList $view      The view to render
     * @param string    $basepath  The base path
     * @param array     $urlparams Additional url params
     * @param array     $addparams Add params
     *
     * @return string The html markup
     *
     * @throws \Throwable
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderThumbWidget(Environment $env, AdminList $view, $basepath, array $urlparams = [], array $addparams = [])
    {
        $filterBuilder = $view->getFilterBuilder();

        return $env->render(
            '@KunstmaanAdminList/AdminListTwigExtension/thumbwidget.html.twig',
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
