<?php

namespace Kunstmaan\NodeSearchBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;

use Kunstmaan\NodeSearchBundle\Form\NodeSearchAdminType;
use Kunstmaan\NodeSearchBundle\Helper\FormWidgets\SearchFormWidget;

/**
 * NodeListener
 */
class NodeListener
{
    /** @var EntityManager $em */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        $searchWidget = new SearchFormWidget($event->getNode(), $this->em);
        $searchWidget->addType('node_search', new NodeSearchAdminType());

        $tabPane = $event->getTabPane();
        $tabPane->addTab(new Tab('kuma_node.tab.searcher.title', $searchWidget));
    }
}
