<?php

namespace Kunstmaan\NodeSearchBundle\EventListener;

use Kunstmaan\NodeBundle\Event\NodeEvent;

interface NodeIndexUpdateEventListenerInterface
{
    public function onPostPublish(NodeEvent $event);

    public function onPostPersist(NodeEvent $event);

    public function onPostDelete(NodeEvent $event);

    public function onPostUnPublish(NodeEvent $event);
}
