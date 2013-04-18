<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\ListWidget;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\FormWidgets\PageTemplateWidget;
use Kunstmaan\PagePartBundle\Helper\FormWidgets\PagePartWidget;
use Symfony\Component\HttpKernel\KernelInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartConfigurationReader;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

/**
 * NodeListener
 */
class NodeListener
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var PagePartAdminFactory
     */
    private $pagePartAdminFactory;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request              $request              The request
     * @param EntityManager        $em                   The entity manager
     * @param KernelInterface      $kernel               The kernel
     * @param FormFactoryInterface $formFactory          The form factory
     * @param PagePartAdminFactory $pagePartAdminFactory The page part admin factory
     */
    public function __construct(Request $request, EntityManager $em, KernelInterface $kernel, FormFactoryInterface $formFactory, PagePartAdminFactory $pagePartAdminFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->kernel = $kernel;
        $this->pagePartAdminFactory = $pagePartAdminFactory;
        $this->request = $request;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        $page = $event->getPage();
        $tabPane = $event->getTabPane();

        if ($page instanceof HasPageTemplateInterface) {
            $tabPane->addTab(new Tab("Content", new PageTemplateWidget($page, $this->request, $this->em, $this->kernel, $this->formFactory, $this->pagePartAdminFactory)));
        } else if ($page instanceof HasPagePartsInterface) {
            /* @var HasPagePartsInterface $page */
            $pagePartConfigurationReader = new PagePartConfigurationReader($this->kernel);
            $pagePartAdminConfigurators = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

            foreach ($pagePartAdminConfigurators as $index => $pagePartAdminConfiguration) {
                $pagePartWidget = new PagePartWidget($page, $this->request, $this->em, $pagePartAdminConfiguration, $this->formFactory, $this->pagePartAdminFactory);
                if ($index == 0) {
                    /* @var Tab $propertiesTab */
                    $propertiesTab = $tabPane->getTabByTitle('Properties');

                    if (!is_null($propertiesTab)) {
                        $propertiesWidget = $propertiesTab->getWidget();
                        $tabPane->removeTab($propertiesTab);
                        $tabPane->addTab(new Tab($pagePartAdminConfiguration->getName(), new ListWidget(array($propertiesWidget, $pagePartWidget))), 0);

                        return;
                    }
                }
                $tabPane->addTab(new Tab($pagePartAdminConfiguration->getName(), $pagePartWidget), sizeof($tabPane->getTabs()));


            }
        }
    }

}
