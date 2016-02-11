<?php

namespace Kunstmaan\NodeSearchBundle\EventListener;

use Kunstmaan\NodeBundle\Event\NodeEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * EventListener which will be triggered when a Node has been updated in order to update its related documents
 * in the index
 */
class NodeIndexUpdateEventListener implements NodeIndexUpdateEventListenerInterface
{
    /** @var ContainerInterface $container */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param NodeEvent $event
     */
    public function onPostPublish(NodeEvent $event)
    {
        $this->index($event);
    }

    /**
     * @param NodeEvent $event
     */
    public function onPostPersist(NodeEvent $event)
    {
        $this->index($event);
    }

    /**
     * @param NodeEvent $event
     */
    private function index(NodeEvent $event)
    {
        $nodeSearchConfiguration = $this->container->get('kunstmaan_node_search.search_configuration.node');
        $nodeSearchConfiguration->indexNodeTranslation($event->getNodeTranslation(), true);
    }

    /**
     * @param NodeEvent $event
     */
    public function onPostDelete(NodeEvent $event)
    {
        $this->delete($event);
    }

    /**
     * @param NodeEvent $event
     */
    public function onPostUnPublish(NodeEvent $event)
    {
        $this->delete($event);
    }

    /**
     * @param NodeEvent $event
     */
    public function delete(NodeEvent $event)
    {
        $nodeSearchConfiguration = $this->container->get('kunstmaan_node_search.search_configuration.node');
        $nodeSearchConfiguration->deleteNodeTranslation($event->getNodeTranslation());
    }
}
