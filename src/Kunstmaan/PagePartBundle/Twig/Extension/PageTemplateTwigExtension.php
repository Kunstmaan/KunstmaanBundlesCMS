<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService;

/**
 * PagePartTwigExtension
 */
class PageTemplateTwigExtension extends \Twig_Extension
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
        return array(
            new \Twig_SimpleFunction('render_pagetemplate', [$this, 'renderPageTemplate'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('getpagetemplate', [$this, 'getPageTemplate']),
            new \Twig_SimpleFunction('render_pagetemplate_configuration', [$this, 'renderPageTemplateConfiguration'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
        );
    }

    /**
     * @param \Twig_Environment        $env
     * @param array                    $twigContext
     * @param HasPageTemplateInterface $page
     * @param array                    $parameters
     *
     * @return string
     */
    public function renderPageTemplate(\Twig_Environment $env, array $twigContext, HasPageTemplateInterface $page, array $parameters = array())
    {
        $pageTemplates = $this->templateConfiguration->getPageTemplates($page);

        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];

        $template = $env->loadTemplate($pageTemplate->getTemplate());

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
     * @param \Twig_Environment        $env
     * @param array                    $twigContext
     * @param HasPageTemplateInterface $page
     * @param array                    $parameters
     *
     * @return string
     */
    public function renderPageTemplateConfiguration(\Twig_Environment $env, array $twigContext, HasPageTemplateInterface $page, array $parameters = array())
    {
        $pageTemplates = $this->templateConfiguration->getPageTemplates($page);

        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];

        $template = $env->loadTemplate($parameters['template']);

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
