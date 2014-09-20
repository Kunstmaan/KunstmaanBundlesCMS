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
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'tabs_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html')))
        );
    }

    /**
     * @param TabPane $tabPane  The tab pane
     * @param array   $options  The extra options
     * @param string  $template The template
     *
     * @return string
     */
    public function renderWidget(TabPane $tabPane, $options = array(), $template = "KunstmaanAdminBundle:TabsTwigExtension:widget.html.twig")
    {
        $template = $this->environment->loadTemplate($template);

        return $template->render(array_merge($options, array(
            'tabPane' => $tabPane
        )));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tabs_twig_extension';
    }

}
