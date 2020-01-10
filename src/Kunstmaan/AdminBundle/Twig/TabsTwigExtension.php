<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension to render tabs
 *
 * @final since 5.4
 */
class TabsTwigExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('tabs_widget', array($this, 'renderWidget'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    /**
     * @param Environment $env
     * @param TabPane     $tabPane  The tab pane
     * @param array       $options  The extra options
     * @param string      $template The template
     *
     * @return string
     */
    public function renderWidget(Environment $env, TabPane $tabPane, $options = array(), $template = '@KunstmaanAdmin/TabsTwigExtension/widget.html.twig')
    {
        $template = $env->load($template);

        return $template->render(array_merge($options, array(
            'tabPane' => $tabPane,
        )));
    }
}
