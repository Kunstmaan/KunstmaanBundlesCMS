<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Twig_Environment;
use Twig_Extension;

/**
 * Extension to render tabs
 */
class TabsTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('tabs_widget', array($this, 'renderWidget'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    /**
     * @param \Twig_Environment $env
     * @param TabPane           $tabPane  The tab pane
     * @param array             $options  The extra options
     * @param string            $template The template
     *
     * @return string
     */
    public function renderWidget(Twig_Environment $env, TabPane $tabPane, $options = array(), $template = 'KunstmaanAdminBundle:TabsTwigExtension:widget.html.twig')
    {
        $template = $env->loadTemplate($template);

        return $template->render(array_merge($options, array(
            'tabPane' => $tabPane,
        )));
    }
}
