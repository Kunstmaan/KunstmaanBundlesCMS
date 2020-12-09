<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\PageTabInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class EntityTabListener
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(RequestStack $requestStack, FormFactoryInterface $formFactory)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->formFactory = $formFactory;
    }

    public function addTabs(TabPane $tabPane, PageTabInterface $page)
    {
        foreach ($page->getTabs() as $pageTab) {
            $formWidget = new FormWidget();
            $formWidget->addType($pageTab->getInternalName(), $pageTab->getFormTypeClass(), $page);
            $tabPane->addTab(new Tab($pageTab->getTabTitle(), $formWidget), $pageTab->getPosition());
        }
    }

    public function adaptForm(AdaptSimpleFormEvent $event)
    {
        $entity = $event->getData();
        $tabPane = $event->getTabPane();

        if ($entity instanceof HasNodeInterface) {
            return;
        }

        if ($entity instanceof PageTabInterface === false) {
            return;
        }

        if ($tabPane instanceof TabPane === false) {
            $tabPane = new TabPane('id', $this->request, $this->formFactory);
        }

        $this->addTabs($tabPane, $entity);

        $tabPane->buildForm();

        $event->setTabPane($tabPane);
    }
}
