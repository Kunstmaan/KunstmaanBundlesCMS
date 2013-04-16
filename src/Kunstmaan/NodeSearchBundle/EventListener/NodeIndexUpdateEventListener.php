<?php

namespace Kunstmaan\NodeSearchBundle\EventListener;

use Kunstmaan\NodeBundle\Event\NodeEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NodeIndexUpdateEventListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onPostPublish(NodeEvent $event)
    {
        $this->index($event);
    }

    public function onPostPersist(NodeEvent $event)
    {
        $this->index($event);
    }

    private function index(NodeEvent $event)
    {
        $nodeSearchConfiguration = $this->container->get('kunstmaan_node_search.searchconfiguration.node');
        $nodeSearchConfiguration->indexNode($event->getNode(), $event->getNodeTranslation()->getLang());
    }

    public function onPostDelete(NodeEvent $event)
    {
        $this->delete($event);
    }

    public function onPostUnPublish(NodeEvent $event)
    {
        $this->delete($event);
    }

    /**
     * @param \Kunstmaan\NodeBundle\Event\NodeEvent $event
     */
    public function delete(NodeEvent $event)
    {
        $nodeSearchConfiguration = $this->container->get('kunstmaan_node_search.searchconfiguration.node');
        $nodeSearchConfiguration->deleteNodeTranslation($event->getNodeTranslation());
    }

}
