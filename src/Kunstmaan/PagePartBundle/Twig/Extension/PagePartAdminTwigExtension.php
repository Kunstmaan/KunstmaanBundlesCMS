<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Symfony\Component\Form\FormView;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PagePartAdminTwigExtension extends AbstractExtension
{
    /** @var bool */
    private $usesExtendedPagePartChooser = false;

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pagepartadmin_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
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
     * @param PagePartAdmin $ppAdmin      The pagepart admin to render
     * @param FormView      $form         The form
     * @param array         $parameters   Additional variables passed to the template
     * @param string        $templateName
     *
     * @return string The html markup
     */
    public function renderWidget(Environment $env, PagePartAdmin $ppAdmin, $form = null, array $parameters = [], $templateName = null): string
    {
        if ($templateName === null) {
            $templateName = '@KunstmaanPagePart/PagePartAdminTwigExtension/widget.html.twig';
        }

        $template = $env->load($templateName);

        return $template->render(array_merge($parameters, [
            'pagepartadmin' => $ppAdmin,
            'page' => $ppAdmin->getPage(),
            'form' => $form,
            'extended' => $this->usesExtendedPagePartChooser,
        ]));
    }

    public function getUsesExtendedPagePartChooser(): bool
    {
        return $this->usesExtendedPagePartChooser;
    }

    /**
     * @param bool $usesExtendedPagePartChooser
     */
    public function setUsesExtendedPagePartChooser($usesExtendedPagePartChooser)
    {
        $this->usesExtendedPagePartChooser = $usesExtendedPagePartChooser;
    }
}
