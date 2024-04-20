<?php

namespace Kunstmaan\TaggingBundle\EventListener;

use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use DoctrineExtensions\Taggable\Taggable;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\PagePartBundle\Event\PagePartEvent;
use Kunstmaan\TaggingBundle\Entity\TagManager;

class TagsListener
{
    /**
     * @var TagManager
     */
    protected $tagManager;

    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    /**
     * @return TagManager
     */
    public function getTagManager()
    {
        return $this->tagManager;
    }

    public function postLoad(PostLoadEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Taggable) {
            $this->getTagManager()->loadTagging($entity);
        }
    }

    /**
     * Runs the postPersist doctrine event and updates the current flag if needed
     */
    public function postPersist(PostPersistEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Taggable) {
            $this->getTagManager()->saveTagging($entity);
        }
    }

    /**
     * Runs the postUpdate doctrine event and updates the current flag if needed
     */
    public function postUpdate(PostUpdateEventArgs $args)
    {
        $this->postPersist($args);
    }

    public function postNodePersist(NodeEvent $event)
    {
        $page = $event->getPage();

        if ($page instanceof Taggable) {
            $this->getTagManager()->saveTagging($page);
        }
    }

    public function postPagePartPersist(PagePartEvent $event)
    {
        $pagePart = $event->getPagePart();

        if ($pagePart instanceof Taggable) {
            $this->getTagManager()->saveTagging($pagePart);
        }
    }
}
