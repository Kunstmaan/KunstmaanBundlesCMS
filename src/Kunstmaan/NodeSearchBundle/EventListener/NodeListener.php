<?php

namespace Kunstmaan\NodeSearchBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeSearchBundle\Form\NodeSearchAdminType;
use Kunstmaan\NodeSearchBundle\Helper\FormWidgets\SearchFormWidget;

/**
 * NodeListener
 */
class NodeListener
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function adaptForm(AdaptFormEvent $event)
    {
        $searchWidget = new SearchFormWidget($event->getNode(), $this->em);
        $searchWidget->addType('node_search', new NodeSearchAdminType());

        $tabPane = $event->getTabPane();
        $tabPane->addTab(new Tab('kuma_node.tab.searcher.title', $searchWidget));
    }
}
