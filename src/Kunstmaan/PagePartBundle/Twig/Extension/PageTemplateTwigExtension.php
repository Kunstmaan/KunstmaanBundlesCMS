<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\PageTemplateConfigurationReader;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * PagePartTwigExtension
 */
class PageTemplateTwigExtension extends \Twig_Extension
{

    protected $em;

    /**
     * @var KernelInterface::
     */
    protected $kernel;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param EntityManager   $em     The entity manager
     * @param KernelInterface $kernel The kernel
     */
    public function __construct(EntityManager $em, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
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
            'render_pagetemplate'  => new \Twig_Function_Method($this, 'renderPageTemplate', array('needs_context' => true, 'is_safe' => array('html'))),
            'getpagetemplate'  => new \Twig_Function_Method($this, 'getPageTemplate')
        );
    }

    /**
     * @param array                    $twigContext The twig context
     * @param HasPageTemplateInterface $page        The page
     * @param array                    $parameters  Some extra parameters
     *
     * @return string
     */
    public function renderPageTemplate(array $twigContext, HasPageTemplateInterface $page, array $parameters = array())
    {
        $pageTemplateConfigurationReader = new PageTemplateConfigurationReader($this->kernel);
        $pageTemplates = $pageTemplateConfigurationReader->getPageTemplates($page);

        /* @var $pageTemplate PageTemplate */
        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];

        $template = $this->environment->loadTemplate($pageTemplate->getTemplate());

        return $template->render($twigContext);
    }

    /**
     * @param HasPageTemplateInterface $page The page
     *
     * @return string
     */
    public function getPageTemplate(HasPageTemplateInterface $page)
    {
        /**@var $entityRepository PageTemplateConfigurationRepository */
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PageTemplateConfiguration');
        $entityRepository->setContainer($this->kernel->getContainer());
        $pageTemplateConfiguration = $entityRepository->findOrCreateFor($page);

        return $pageTemplateConfiguration->getPageTemplate();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pagetemplate_twig_extension';
    }

}
