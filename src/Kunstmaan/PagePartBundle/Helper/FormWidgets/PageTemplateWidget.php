<?php

namespace Kunstmaan\PagePartBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateInterface;
use Kunstmaan\PagePartBundle\PageTemplate\Region;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * PageTemplateWidget
 */
class PageTemplateWidget extends FormWidget
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PagePartAdminFactory
     */
    private $pagePartAdminFactory;

    /**
     * @var PageInterface
     */
    private $page;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var PagePartWidget[]
     */
    private $widgets = array();

    /**
     * @var PageTemplateInterface[]
     */
    private $pageTemplates = array();

    /**
     * @var PagePartAdminConfiguratorInterface[]
     */
    private $pagePartAdminConfigurations = array();

    /**
     * @var PageTemplateConfiguration
     */
    protected $pageTemplateConfiguration;

    /**
     * @param HasPageTemplateInterface                 $page
     * @param Request                                  $request
     * @param EntityManagerInterface                   $em
     * @param PagePartAdminFactory                     $pagePartAdminFactory
     * @param PageTemplateConfigurationReaderInterface $templateReader
     * @param PagePartConfigurationReaderInterface     $pagePartReader
     * @param PageTemplateConfigurationService         $pageTemplateConfigurationService
     */
    public function __construct(
        HasPageTemplateInterface $page,
        Request $request,
        EntityManagerInterface $em,
        PagePartAdminFactory $pagePartAdminFactory,
        PageTemplateConfigurationReaderInterface $templateReader,
        PagePartConfigurationReaderInterface $pagePartReader,
        PageTemplateConfigurationService $pageTemplateConfigurationService
    ) {
        parent::__construct();

        $this->page = $page;
        $this->em = $em;
        $this->request = $request;
        $this->pagePartAdminFactory = $pagePartAdminFactory;

        $this->pageTemplates = $templateReader->getPageTemplates($page);
        $this->pagePartAdminConfigurations = $pagePartReader->getPagePartAdminConfigurators($page);
        $this->pageTemplateConfiguration = $pageTemplateConfigurationService->findOrCreateFor($page);

        foreach ($this->getPageTemplate()->getRows() as $row) {
            foreach ($row->getRegions() as $region) {
                $this->processRegion($region);
            }
        }
    }

    /**
     * @param Region $region The region
     */
    private function processRegion($region)
    {
        if (count($region->getChildren())) {
            foreach ($region->getChildren() as $child) {
                $this->processRegion($child);
            }
        } else {
            $this->loadWidgets($region);
        }
    }

    /**
     * @param Region $region The region
     */
    private function loadWidgets($region)
    {
        $pagePartAdminConfiguration = null;
        foreach ($this->pagePartAdminConfigurations as $ppac) {
            if ($ppac->getContext() == $region->getName()) {
                $pagePartAdminConfiguration = $ppac;
            }
        }

        if ($pagePartAdminConfiguration !== null) {
            $pagePartWidget = new PagePartWidget($this->page, $this->request, $this->em, $pagePartAdminConfiguration, $this->pagePartAdminFactory);
            $this->widgets[$region->getName()] = $pagePartWidget;
        }
    }

    /**
     * @return PageTemplateInterface
     */
    public function getPageTemplate()
    {
        return $this->pageTemplates[$this->pageTemplateConfiguration->getPageTemplate()];
    }

    /**
     * @return PageTemplateInterface[]
     */
    public function getPageTemplates()
    {
        return $this->pageTemplates;
    }

    /**
     * @return PageInterface|HasPageTemplateInterface
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
        $configurationname = $request->get('pagetemplate_template');
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
        return [];
    }
}
