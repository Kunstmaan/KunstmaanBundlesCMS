<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;
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
     * @param EntityManager        $em                   The entity manager
     * @param KernelInterface      $kernel               The kernel
     * @param FormFactoryInterface $formFactory          The form factory
     * @param PagePartAdminFactory $pagePartAdminFactory The page part admin factory
     */
    public function __construct(EntityManager $em, KernelInterface $kernel, FormFactoryInterface $formFactory, PagePartAdminFactory $pagePartAdminFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->kernel = $kernel;
        $this->pagePartAdminFactory = $pagePartAdminFactory;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        $page = $event->getPage();
        $tabPane = $event->getTabPane();

        if ($page instanceof HasPageTemplateInterface) {
            $pageTemplateWidget = new PageTemplateWidget($page, $event->getRequest(), $this->em, $this->kernel, $this->formFactory, $this->pagePartAdminFactory);
            /* @var Tab $propertiesTab */
            $propertiesTab = $tabPane->getTabByTitle('Properties');
            if (!is_null($propertiesTab)) {
                $propertiesWidget = $propertiesTab->getWidget();
                $tabPane->removeTab($propertiesTab);
                $tabPane->addTab(new Tab("Content", new ListWidget(array($propertiesWidget, $pageTemplateWidget))), 0);
            } else {
                $tabPane->addTab(new Tab("Content", $pageTemplateWidget), 0);
            }
        } else if ($page instanceof HasPagePartsInterface) {
            /* @var HasPagePartsInterface $page */
            $pagePartConfigurationReader = new PagePartConfigurationReader($this->kernel);
            $pagePartAdminConfigurators = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

            foreach ($pagePartAdminConfigurators as $index => $pagePartAdminConfiguration) {
                $pagePartWidget = new PagePartWidget($page, $event->getRequest(), $this->em, $pagePartAdminConfiguration, $this->formFactory, $this->pagePartAdminFactory);
                if ($index == 0) {
                    /* @var Tab $propertiesTab */
                    $propertiesTab = $tabPane->getTabByTitle('Properties');

                    if (!is_null($propertiesTab)) {
                        $propertiesWidget = $propertiesTab->getWidget();
                        $tabPane->removeTab($propertiesTab);
                        $tabPane->addTab(new Tab($pagePartAdminConfiguration->getName(), new ListWidget(array($propertiesWidget, $pagePartWidget))), 0);

                        continue;
                    }
                }
                $tabPane->addTab(new Tab($pagePartAdminConfiguration->getName(), $pagePartWidget), sizeof($tabPane->getTabs()));


            }
        }
    }

}
