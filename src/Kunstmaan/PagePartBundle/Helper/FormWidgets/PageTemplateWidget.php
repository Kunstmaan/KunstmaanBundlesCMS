<?php

namespace Kunstmaan\PagePartBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\PageTemplateConfigurationReader;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Symfony\Component\HttpKernel\KernelInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartConfigurationReader;

/**
 * PageTemplateWidget
 */
class PageTemplateWidget extends FormWidget
{

    /**
     * @var AbstractPagePartAdminConfigurator
     */
    protected $pagePartAdminConfigurator;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var PagePartAdminFactory
     */
    protected $pagePartAdminFactory;

    /**
     * @var PagePartAdmin
     */
    protected $pagePartAdmin;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $widgets = array();

    /**
     * @var PageTemplate[]
     */
    protected $pageTemplates = array();

    /**
     * @var AbstractPagePartAdminConfigurator[]
     */
    protected $pagePartAdminConfigurations = array();

    /**
     * @var PageTemplateConfiguration
     */
    protected $pageTemplateConfiguration;

    /**
     * @param HasPageTemplateInterface $page                 The page
     * @param Request                  $request              The request
     * @param EntityManager            $em                   The entity manager
     * @param KernelInterface          $kernel               The kernel
     * @param FormFactoryInterface     $formFactory          The form factory
     * @param PagePartAdminFactory     $pagePartAdminFactory The page part admin factory
     */
    public function __construct(HasPageTemplateInterface $page, Request $request, EntityManager $em, KernelInterface $kernel, FormFactoryInterface $formFactory, PagePartAdminFactory $pagePartAdminFactory)
    {
        parent::__construct();

        $this->page = $page;
        $this->em = $em;
        $this->request = $request;
        $pageTemplateConfigurationReader = new PageTemplateConfigurationReader($kernel);
        $this->pageTemplates = $pageTemplateConfigurationReader->getPageTemplates($page);
        $pagePartConfigurationReader = new PagePartConfigurationReader($kernel);
        $this->pagePartAdminConfigurations = $pagePartConfigurationReader->getPagePartAdminConfigurators($this->page);
        $repo = $this->em->getRepository('KunstmaanPagePartBundle:PageTemplateConfiguration');
        $repo->setContainer($kernel->getContainer());
        $this->pageTemplateConfiguration = $repo->findOrCreateFor($page);

        foreach ($this->getPageTemplate()->getRows() as $row) {
            foreach ($row->getRegions() as $region) {
                $pagePartAdminConfiguration = null;
                foreach ($this->pagePartAdminConfigurations as $ppac) {
                    if ($ppac->getContext() == $region->getName()) {
                        $pagePartAdminConfiguration = $ppac;
                    }
                }
                if ($pagePartAdminConfiguration !== null) {
                    $pagePartWidget = new PagePartWidget($page, $this->request, $this->em, $pagePartAdminConfiguration, $formFactory, $pagePartAdminFactory);
                    $this->widgets[$region->getName()] = $pagePartWidget;
                }
            }
        }
    }

    /**
     * @return PageTemplate
     */
    public function getPageTemplate()
    {
        return $this->pageTemplates[$this->pageTemplateConfiguration->getPageTemplate()];
    }

    /**
     * @return PageTemplate
     */
    public function getPageTemplates()
    {
        return $this->pageTemplates;
    }

    /**
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        foreach ($this->widgets as $widget) {
            $widget->buildForm($builder);
        }
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $configurationname = $request->get("pagetemplate_template");
        $this->pageTemplateConfiguration->setPageTemplate($configurationname);
        foreach ($this->widgets as $widget) {
            $widget->bindRequest($request);
        }
    }

    /**
     * @param EntityManager $em The entity manager
     */
    public function persist(EntityManager $em)
    {
        $em->persist($this->pageTemplateConfiguration);
        foreach ($this->widgets as $widget) {
            $widget->persist($em);
        }
    }

    /**
     * @param FormView $formView
     *
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        $errors = array();

        foreach ($this->widgets as $widget) {
            $errors = array_merge($errors, $widget->getFormErrors($formView));
        }

        return $errors;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanPagePartBundle:FormWidgets\PageTemplateWidget:widget.html.twig';
    }

    /**
     * @param string $name
     *
     * @return PagePartAdmin
     */
    public function getFormWidget($name)
    {
        if (array_key_exists($name, $this->widgets)) {
            return $this->widgets[$name];
        }

        return null;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        $params = array();
        return $params;
    }

}
