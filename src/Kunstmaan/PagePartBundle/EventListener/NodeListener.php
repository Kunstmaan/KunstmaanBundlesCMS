<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\ListWidget;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\FormWidgets\PageTemplateWidget;
use Kunstmaan\PagePartBundle\Helper\FormWidgets\PagePartWidget;

/**
 * NodeListener
 */
class NodeListener
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
     * @var PageTemplateConfigurationReaderInterface
     */
    private $templateReader;

    /**
     * @var PagePartConfigurationReaderInterface
     */
    private $pagePartReader;

    /**
     * @var PageTemplateConfigurationService
     */
    private $pageTemplateConfiguratiorService;

    public function __construct(
        EntityManagerInterface $em,
        PagePartAdminFactory $pagePartAdminFactory,
        PageTemplateConfigurationReaderInterface $templateReader,
        PagePartConfigurationReaderInterface $pagePartReader,
        PageTemplateConfigurationService $pageTemplateConfiguratiorService
    )
    {
        $this->em = $em;
        $this->pagePartAdminFactory = $pagePartAdminFactory;
        $this->templateReader = $templateReader;
        $this->pagePartReader = $pagePartReader;
        $this->pageTemplateConfiguratiorService = $pageTemplateConfiguratiorService;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        $page = $event->getPage();
        $tabPane = $event->getTabPane();

        if ($page instanceof HasPageTemplateInterface) {
            $pageTemplateWidget = new PageTemplateWidget($page, $event->getRequest(), $this->em, $this->pagePartAdminFactory, $this->templateReader, $this->pagePartReader, $this->pageTemplateConfiguratiorService);

            /* @var Tab $propertiesTab */
            $propertiesTab = $tabPane->getTabByTitle('kuma_node.tab.properties.title');
            if (!is_null($propertiesTab)) {
                $propertiesWidget = $propertiesTab->getWidget();
                $tabPane->removeTab($propertiesTab);
                $tabPane->addTab(new Tab("kuma_pagepart.tab.content.title", new ListWidget(array($propertiesWidget, $pageTemplateWidget))), 0);
            } else {
                $tabPane->addTab(new Tab("kuma_pagepart.tab.content.title", $pageTemplateWidget), 0);
            }
        } else if ($page instanceof HasPagePartsInterface) {
            /* @var HasPagePartsInterface $page */
            $pagePartAdminConfigurators = $this->pagePartReader->getPagePartAdminConfigurators($page);

            foreach ($pagePartAdminConfigurators as $index => $pagePartAdminConfiguration) {
                $pagePartWidget = new PagePartWidget($page, $event->getRequest(), $this->em, $pagePartAdminConfiguration, $this->pagePartAdminFactory);
                if ($index == 0) {
                    /* @var Tab $propertiesTab */
                    $propertiesTab = $tabPane->getTabByTitle('kuma_node.tab.properties.title');

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
