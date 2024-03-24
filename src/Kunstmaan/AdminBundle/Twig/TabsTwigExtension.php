<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension to render tabs
 */
final class TabsTwigExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('tabs_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param TabPane $tabPane  The tab pane
     * @param array   $options  The extra options
     * @param string  $template The template
     */
    public function renderWidget(Environment $env, TabPane $tabPane, $options = [], $template = '@KunstmaanAdmin/TabsTwigExtension/widget.html.twig'): string
    {
        $template = $env->load($template);

        return $template->render(array_merge($options, [
            'tabPane' => $tabPane,
        ]));
    }
}
