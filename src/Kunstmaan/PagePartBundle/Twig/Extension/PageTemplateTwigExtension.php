<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * PagePartTwigExtension
 *
 * @final since 5.4
 */
class PageTemplateTwigExtension extends AbstractExtension
{
    /**
     * @var PageTemplateConfigurationService
     */
    private $templateConfiguration;

    public function __construct(PageTemplateConfigurationService $templateConfiguration)
    {
        $this->templateConfiguration = $templateConfiguration;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('render_pagetemplate', [$this, 'renderPageTemplate'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction('getpagetemplate', [$this, 'getPageTemplate']),
            new TwigFunction('render_pagetemplate_configuration', [$this, 'renderPageTemplateConfiguration'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @return string
     */
    public function renderPageTemplate(Environment $env, array $twigContext, HasPageTemplateInterface $page, array $parameters = [])
    {
        $pageTemplates = $this->templateConfiguration->getPageTemplates($page);

        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];

        $template = $env->load($pageTemplate->getTemplate());

        return $template->render(array_merge($parameters, $twigContext));
    }

    /**
     * @param HasPageTemplateInterface $page The page
     *
     * @return string
     */
    public function getPageTemplate(HasPageTemplateInterface $page)
    {
        return $this->templateConfiguration->findOrCreateFor($page)->getPageTemplate();
    }

    /**
     * @return string
     */
    public function renderPageTemplateConfiguration(Environment $env, array $twigContext, HasPageTemplateInterface $page, array $parameters = [])
    {
        $pageTemplates = $this->templateConfiguration->getPageTemplates($page);

        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];

        $template = $env->load($parameters['template']);

        return $template->render(array_merge(['pageTemplate' => $pageTemplate], $twigContext));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pagetemplate_twig_extension';
    }
}
