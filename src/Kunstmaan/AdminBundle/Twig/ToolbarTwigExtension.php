<?php

namespace Kunstmaan\AdminBundle\Twig;

use Twig_Environment;
use Twig_Extension;

/**
 * Extension to render blocks of twig templates
 */
class ToolbarTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('block_render', array($this, 'renderBlock'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    /**
     * @param Twig_Environment $env
     * @param $template
     * @param $block
     * @param $context
     *
     * @return string
     */
    public function renderBlock(Twig_Environment $env, $template, $block, $context)
    {
        $template = $env->loadTemplate($template);
        $context = $env->mergeGlobals($context);

        return $template->renderBlock($block, $context);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'toolbar_twig_extension';
    }
}
