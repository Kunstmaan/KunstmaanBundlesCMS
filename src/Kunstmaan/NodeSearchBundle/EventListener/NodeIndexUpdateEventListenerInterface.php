<?php

namespace Kunstmaan\NodeSearchBundle\EventListener;

use Kunstmaan\NodeBundle\Event\NodeEvent;

interface NodeIndexUpdateEventListenerInterface
{
    /**
     * @param NodeEvent $event
     */
    public function onPostPublish(NodeEvent $event);

    /**
     * @param NodeEvent $event
     */
    public function onPostPersist(NodeEvent $event);

    /**
     * @param NodeEvent $event
     */
    public function onPostDelete(NodeEvent $event);

    /**
     * @param NodeEvent $event
     */
    public function onPostUnPublish(NodeEvent $event);
}
