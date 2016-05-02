<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;

/**
 * PagePartAdminTwigExtension
 */
class PagePartAdminTwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pagepartadmin_widget', array($this, 'renderWidget'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    /**
     * Renders the HTML for a given pagepart
     *
     * Example usage in Twig:
     *
     *     {{ pagepartadmin_widget(ppAdmin) }}
     *
     * You can pass options during the call:
     *
     *     {{ pagepartadmin_widget(ppAdmin, {'attr': {'class': 'foo'}}) }}
     *
     *     {{ pagepartadmin_widget(ppAdmin, {'separator': '+++++'}) }}
     *
     * @param \Twig_Environment $env
     * @param PagePartAdmin $ppAdmin The pagepart admin to render
     * @param Form $form The form
     * @param array $parameters Additional variables passed to the template
     * @param string $templateName
     * @return string The html markup
     */
    public function renderWidget(
        \Twig_Environment $env,
        PagePartAdmin $ppAdmin ,
        $form = null,
        array $parameters = array(),
        $templateName = 'KunstmaanPagePartBundle:PagePartAdminTwigExtension:widget.html.twig'
    )
    {
        $template = $env->loadTemplate($templateName);

        return $template->render(array_merge($parameters, array(
            'pagepartadmin' => $ppAdmin,
            'form' => $form
        )));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pagepartadmin_twig_extension';
    }
}
