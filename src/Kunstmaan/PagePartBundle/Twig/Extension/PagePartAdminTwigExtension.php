<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;

/**
 * PagePartAdminTwigExtension
 */
class PagePartAdminTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'pagepartadmin_widget'  => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
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
     * @param PagePartAdmin $ppAdmin    The pagepart admin to render
     * @param Form          $form       The form
     * @param array         $parameters Additional variables passed to the template
     *
     * @return string The html markup
     */
    public function renderWidget(PagePartAdmin $ppAdmin , $form = null , array $parameters = array())
    {
        $template = $this->environment->loadTemplate("KunstmaanPagePartBundle:PagePartAdminTwigExtension:widget.html.twig");

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
