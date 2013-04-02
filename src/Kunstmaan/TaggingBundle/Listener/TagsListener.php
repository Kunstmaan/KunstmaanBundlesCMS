<?php

namespace Kunstmaan\TaggingBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use DoctrineExtensions\Taggable\Taggable;

use Kunstmaan\AdminNodeBundle\Helper\Event\PageEvent;
use Kunstmaan\MediaBundle\Helper\Event\MediaEvent;
use Kunstmaan\TaggingBundle\Entity\TagManager;

class TagsListener
{

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getTagManager()
    {
        return $this->container->get('kuma_tagging.tag_manager');
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Taggable) {
            $this->getTagManager()->loadTagging($entity);
        }
    }

    /**
     * Runs the postPersist doctrine event and updates the current flag if needed
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Taggable) {
            $this->getTagManager()->saveTagging($entity);
        }
    }

    /**
     * Runs the postUpdate doctrine event and updates the current flag if needed
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postPersist($args);
    }

    public function postNodePersist(PageEvent $event)
    {
        $page = $event->getPage();

        if ($page instanceof Taggable) {
            $this->getTagManager()->saveTagging($page);
        }
    }

    public function postMediaEdit(MediaEvent $event)
    {
        $metadata = $event->getMetadata();

        if (isset($metadata) && $metadata instanceof Taggable) {
            $this->getTagManager()->saveTagging($metadata);
        }
    }

    public function postMediaCreate(MediaEvent $event)
    {
        $metadata = $event->getMetadata();

        if (isset($metadata) && $metadata instanceof Taggable) {
            $this->getTagManager()->saveTagging($metadata);
        }
    }

}
