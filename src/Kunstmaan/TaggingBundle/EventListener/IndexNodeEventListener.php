<?php

namespace Kunstmaan\TaggingBundle\EventListener;

use DoctrineExtensions\Taggable\Taggable;

class IndexNodeEventListener
{
    public function onIndexNode($event)
    {
        $page = $event->getPage();
        if ($page instanceof Taggable) {
            $tags = array();
            foreach ($page->getTags() as $tag) {
                $tags[] = $tag->getName();
            }
            $event->doc['tags'] = $tags;
        }
    }
}
