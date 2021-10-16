<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\NodeBundle\Event\CopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeBundle\Event\RecopyPageTranslationNodeEvent;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogPageEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param Logger                $logger       The logger
     * @param TokenStorageInterface $tokenStorage The security token storage
     */
    public function __construct(Logger $logger, TokenStorageInterface $tokenStorage)
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::COPY_PAGE_TRANSLATION => 'onCopyPageTranslation',
            Events::RECOPY_PAGE_TRANSLATION => 'onRecopyPageTranslation',
            Events::ADD_EMPTY_PAGE_TRANSLATION => 'onAddEmptyPageTranslation',
            Events::POST_PUBLISH => 'postPublish',
            Events::POST_UNPUBLISH => 'postUnPublish',
            Events::POST_DELETE => 'postDelete',
            Events::ADD_NODE => 'onAddNode',
            Events::POST_PERSIST => 'postPersist',
            Events::CREATE_PUBLIC_VERSION => 'onCreatePublicVersion',
            Events::CREATE_DRAFT_VERSION => 'onCreateDraftVersion',
        ];
    }

    private function getUser(): UserInterface
    {
        if (\is_null($this->user)) {
            $this->user = $this->tokenStorage->getToken()->getUser();
        }

        return $this->user;
    }

    public function onCopyPageTranslation(CopyPageTranslationNodeEvent $event)
    {
        $this->logger->info(sprintf('%s just copied the page translation from %s (%d) to %s (%d) for node with id %d', $this->getUsername(), $event->getOriginalLanguage(), $event->getOriginalPage()->getId(), $event->getNodeTranslation()->getLang(), $event->getPage()->getId(), $event->getNode()->getId()));
    }

    public function onRecopyPageTranslation(RecopyPageTranslationNodeEvent $event)
    {
        $this->logger->info(sprintf('%s just recopied the page translation from %s (%d) to %s (%d) for node with id %d', $this->getUsername(), $event->getOriginalLanguage(), $event->getOriginalPage()->getId(), $event->getNodeTranslation()->getLang(), $event->getPage()->getId(), $event->getNode()->getId()));
    }

    public function onAddEmptyPageTranslation(NodeEvent $event)
    {
        $this->logger->info(sprintf('%s just added an empty page translation (%d) for node with id %d in language %s', $this->getUsername(), $event->getPage()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function postPublish(NodeEvent $event)
    {
        $this->logger->info(sprintf('%s just published the page with id %d for node %d in language %s', $this->getUsername(), $event->getPage()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function postUnPublish(NodeEvent $event)
    {
        $this->logger->info(sprintf('%s just unpublished the page with id %d for node %d in language %s', $this->getUsername(), $event->getPage()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function postDelete(NodeEvent $event)
    {
        $this->logger->info(sprintf('%s just deleted node with id %d', $this->getUsername(), $event->getNode()->getId()));
    }

    public function onAddNode(NodeEvent $event)
    {
        $this->logger->info(sprintf('%s just added node with id %d in language %s', $this->getUsername(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function postPersist(NodeEvent $event)
    {
        $this->logger->info(sprintf('%s just updated page with id %d for node %d in language %s', $this->getUsername(), $event->getPage()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function onCreatePublicVersion(NodeEvent $event)
    {
        $this->logger->info(sprintf('%s just created a new public version %d for node %d in language %s', $this->getUsername(), $event->getNodeVersion()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function onCreateDraftVersion(NodeEvent $event)
    {
        $this->logger->info(sprintf('%s just created a draft version %d for node %d in language %s', $this->getUsername(), $event->getNodeVersion()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    private function getUsername(): string
    {
        $user = $this->getUser();

        return method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername();
    }
}
