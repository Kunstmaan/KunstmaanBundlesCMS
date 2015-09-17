<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StructureNodeSubscriber implements EventSubscriberInterface
{
    public function onNodeSave(NodeEvent $event)
    {
        $page = $event->getPage();
        if (false === $page->isStructureNode()) {
            return;
        }

        $node = $event->getNode();
        $nodeTranslation = $event->getNodeTranslation();

        $node->setHiddenFromNav(true);
        $nodeTranslation->setOnline(true);
        $nodeTranslation->setUrl('');
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_PERSIST => 'onNodeSave',
            Events::ADD_NODE => 'onNodeSave',
        ];
    }
}