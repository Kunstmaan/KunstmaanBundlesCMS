<?php

namespace Kunstmaan\AdminBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension to render blocks of twig templates
 *
 * @final since 5.4
 */
class ToolbarTwigExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('block_render', [$this, 'renderBlock'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @return string
     */
    public function renderBlock(Environment $env, $template, $block, $context)
    {
        $template = $env->load($template);
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
