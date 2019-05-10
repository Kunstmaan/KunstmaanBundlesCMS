<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;

/**
 * PagePartAdminTwigExtension
 */
class PagePartAdminTwigExtension extends \Twig_Extension
{
    private $usesExtendedPagePartChooser = false;

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pagepartadmin_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
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
     * @param PagePartAdmin     $ppAdmin      The pagepart admin to render
     * @param Form              $form         The form
     * @param array             $parameters   Additional variables passed to the template
     * @param string            $templateName
     *
     * @return string The html markup
     */
    public function renderWidget(\Twig_Environment $env, PagePartAdmin $ppAdmin, $form = null, array $parameters = [], $templateName = null)
    {
        if ($templateName === null) {
            $templateName = 'KunstmaanPagePartBundle:PagePartAdminTwigExtension:widget.html.twig';
        }

        $template = $env->loadTemplate($templateName);

        return $template->render(array_merge($parameters, [
            'pagepartadmin' => $ppAdmin,
            'page' => $ppAdmin->getPage(),
            'form' => $form,
            'extended' => $this->usesExtendedPagePartChooser,
        ]));
    }

    /**
     * Get usesExtendedPagePartChooser.
     *
     * @return usesExtendedPagePartChooser
     */
    public function getUsesExtendedPagePartChooser()
    {
        return $this->usesExtendedPagePartChooser;
    }

    /**
     * Set usesExtendedPagePartChooser.
     *
     * @param usesExtendedPagePartChooser the value to set
     */
    public function setUsesExtendedPagePartChooser($usesExtendedPagePartChooser)
    {
        $this->usesExtendedPagePartChooser = $usesExtendedPagePartChooser;
    }
}
