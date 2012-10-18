<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\CopyPageTranslationPageEvent;
use Kunstmaan\NodeBundle\Event\PageEvent;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @todo right namespace?
 */
class LogPageEventsSubscriber implements EventSubscriberInterface
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var UserInterface
     */
    private $user = null;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger, SecurityContextInterface $securityContext)
    {
        $this->logger = $logger;
        $this->securityContext = $securityContext;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::COPY_PAGE_TRANSLATION => 'onCopyPageTranslation',
            Events::ADD_EMPTY_PAGE_TRANSLATION => 'onAddEmptyPageTranslation',
            Events::POST_PUBLISH => 'postPublish',
            Events::POST_UNPUBLISH => 'postUnPublish',
            Events::POST_DELETE => 'postDelete',
            Events::ADD_NODE => 'onAddNode', // @todo good name?
            Events::POST_PERSIST => 'postPersist',
            Events::CREATE_PUBLIC_VERSION => 'onCreatePublicVersion',
            Events::CREATE_DRAFT_VERSION => 'onCreateDraftVersion',

        );
    }

    private function getUser()
    {
        if (is_null($this->user)) {
            $this->user = $this->securityContext->getToken()->getUser();
        }

        return $this->user;
    }

    public function onCopyPageTranslation(CopyPageTranslationPageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just copied the page translation from %s (%d) to %s (%d) for node with id %d', $this->getUser()->getUsername(), $event->getOriginalLanguage(), $event->getOriginalPage()->getId(), $event->getNodeTranslation()->getLang(), $event->getPage()->getId(), $event->getNode()->getId()));
    }

    public function onAddEmptyPageTranslation(PageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just added an empty page translation (%d) for node with id %d in language %s', $this->getUser()->getUsername(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function postPublish(PageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just published the page with id %d for node %d in language %s', $this->getUser()->getUsername(), $event->getPage()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function postUnPublish(PageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just unpublished the page with id %d for node %d in language %s', $this->getUser()->getUsername(), $event->getPage()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function postDelete(PageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just deleted node with id %d', $this->getUser()->getUsername(), $event->getNode()->getId()));
    }

    public function onAddNode(PageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just added node with id %d in language %s', $this->getUser()->getUsername(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function postPersist(PageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just updated page with id %d for node %d in language %s', $this->getUser()->getUsername(), $event->getPage()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function onCreatePublicVersion(PageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just created a new public version %d for node %d in language %s', $this->getUser()->getUsername(), $event->getNodeVersion()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

    public function onCreateDraftVersion(PageEvent $event)
    {
        $this->logger->addInfo(sprintf('%s just created a draft version %d for node %d in language %s', $this->getUser()->getUsername(), $event->getNodeVersion()->getId(), $event->getNode()->getId(), $event->getNodeTranslation()->getLang()));
    }

}
