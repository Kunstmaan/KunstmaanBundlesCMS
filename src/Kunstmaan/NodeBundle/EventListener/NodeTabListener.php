<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\PageTabInterface;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;

class NodeTabListener
{
    public function addTabs(TabPane $tabPane, PageTabInterface $page)
    {
        foreach ($page->getTabs() as $pageTab) {
            $formWidget = new FormWidget();
            $formWidget->addType($pageTab->getInternalName(), $pageTab->getFormTypeClass(), $page);
            $tabPane->addTab(new Tab($pageTab->getTabTitle(), $formWidget), $pageTab->getPosition());
        }
    }

    public function adaptForm(AdaptFormEvent $event)
    {
        $page = $event->getPage();
        $tabPane = $event->getTabPane();

        if ($page instanceof HasNodeInterface === false) {
            return;
        }

        if ($page->isStructureNode() === true) {
            return;
        }

        /** @var PageTabInterface $page */
        if ($page instanceof PageTabInterface === false) {
            return;
        }

        $this->addTabs($tabPane, $page);
    }
}
