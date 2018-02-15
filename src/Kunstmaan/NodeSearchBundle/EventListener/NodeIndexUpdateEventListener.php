<?php

namespace Kunstmaan\NodeSearchBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NodeIndexUpdateEventListener
 *
 * EventListener which will be triggered when a Node has been updated in order to update its related documents in the index
 *
 * @package Kunstmaan\NodeSearchBundle\EventListener
 */
class NodeIndexUpdateEventListener implements NodeIndexUpdateEventListenerInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var NodePagesConfiguration */
    private $nodePagesConfiguration;

    /** @var array */
    private $entityChangeSet;

    /**
     * @param ContainerInterface|NodePagesConfiguration $nodePagesConfiguration
     */
    public function __construct(/* NodePagesConfiguration */ $nodePagesConfiguration)
    {
        if ($nodePagesConfiguration instanceof ContainerInterface) {
            @trigger_error(
                'Container injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
                E_USER_DEPRECATED
            );
            $this->container = $nodePagesConfiguration;
            $this->nodePagesConfiguration = $this->container->get(NodePagesConfiguration::class);

            return;
        }

        $this->nodePagesConfiguration = $nodePagesConfiguration;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof NodeTranslation) {
            // unfortunately we have to keep a state to see what has changed
            $this->entityChangeSet = [
                'nodeTranslationId' => $args->getObject()->getId(),
                'changeSet' => $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($args->getObject()),
            ];
        }
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
        $reIndexChildren = (
            null !== $this->entityChangeSet
            && $this->entityChangeSet['nodeTranslationId'] == $event->getNodeTranslation()->getId()
            && isset($this->entityChangeSet['changeSet']['url'])
        );
        $this->index($event, $reIndexChildren);
    }

    /**
     * @param NodeEvent $event
     * @param bool      $reIndexChildren
     */
    private function index(NodeEvent $event, $reIndexChildren = false)
    {
        $nodeTranslation = $event->getNodeTranslation();

        if ($this->hasOfflineParents($nodeTranslation)) {
            return;
        }

        $this->nodePagesConfiguration->indexNodeTranslation($nodeTranslation, true);

        if ($reIndexChildren) {
            $this->nodePagesConfiguration->indexChildren($event->getNode(), $nodeTranslation->getLang());
        }
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
        $this->nodePagesConfiguration->deleteNodeTranslation($event->getNodeTranslation());
    }

    /**
     * @param $nodeTranslation
     *
     * @return bool
     */
    private function hasOfflineParents(NodeTranslation $nodeTranslation)
    {
        $lang = $nodeTranslation->getLang();
        foreach ($nodeTranslation->getNode()->getParents() as $node) {
            $nodeNT = $node->getNodeTranslation($lang, true);
            if ($nodeNT && !$nodeNT->isOnline()) {
                return true;
            }
        }

        return false;
    }
}
