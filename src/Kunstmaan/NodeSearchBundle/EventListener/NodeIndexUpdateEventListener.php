<?php

namespace Kunstmaan\NodeSearchBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * EventListener which will be triggered when a Node has been updated in order to update its related documents
 * in the index
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
     * @param ContainerInterface $container
     */
    public function __construct(/* NodePagesConfiguration */ $nodePagesConfiguration)
    {
        if ($nodePagesConfiguration instanceof ContainerInterface) {
            @trigger_error(sprintf('Passing the container as the first argument of "%s" is deprecated in KunstmaanNodeSearchBundle 5.2 and will be removed in KunstmaanNodeSearchBundle 6.0. Inject the "%s" service instead.', __CLASS__, 'kunstmaan_node_search.search_configuration.node'), E_USER_DEPRECATED);

            $this->container = $nodePagesConfiguration;
            $this->nodePagesConfiguration = $this->container->get('kunstmaan_node_search.search_configuration.node');

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
            !is_null($this->entityChangeSet)
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
            if ($nodeNT && !$nodeNT->isOnline() && !$nodeNT instanceof StructureNode) {
                return true;
            }
        }

        return false;
    }
}
