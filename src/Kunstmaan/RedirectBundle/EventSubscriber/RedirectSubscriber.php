<?php

namespace Kunstmaan\RedirectBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\RedirectBundle\Entity\AutoRedirectInterface;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RedirectSubscriber implements EventSubscriber
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var array<string,array<int,Redirect>
     */
    private $redirects = [];

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        $entityManager = $onFlushEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        if (!$entityManager instanceof EntityManager) {
            return;
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof NodeTranslation) {
                continue;
            }

            $this->createAutoRedirect(
                $entity,
                $entityManager,
                $unitOfWork
            );
        }

        $unitOfWork->computeChangeSets();
    }

    private function createAutoRedirect(
        NodeTranslation $nodeTranslation,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork
    ): void {
        $changeSet = $unitOfWork->getEntityChangeSet($nodeTranslation);

        if (!isset($changeSet['url'][0], $changeSet['url'][1])) {
            return;
        }

        $page = $nodeTranslation->getRef($entityManager);

        if (!$page instanceof AutoRedirectInterface) {
            return;
        }

        [
            $oldUrl,
            $newUrl,
        ] = $changeSet['url'];

        $this->processRedirect(
            $entityManager,
            $oldUrl,
            $newUrl
        );
    }

    private function processRedirect(
        EntityManager $entityManager,
        string $oldUrl,
        string $newUrl
    ): void {
        $this->removeOriginRedirects(
            $newUrl,
            $entityManager
        );

        $this->updateTargetRedirects(
            $oldUrl,
            $newUrl,
            $entityManager
        );

        $this->createRedirect(
            $entityManager,
            $oldUrl,
            $newUrl
        );
    }

    private function removeOriginRedirects(
        string $newUrl,
        EntityManager $entityManager
    ): void {
        $redirects = $entityManager->getRepository(Redirect::class)->findBy(
            [
                'origin' => $newUrl,
            ]
        );

        /** @var Redirect $redirect */
        foreach ($redirects as $redirect) {
            $entityManager->remove($redirect);
        }

        if (isset($this->redirects[$newUrl])) {
            foreach ($this->redirects[$newUrl] as $redirect) {
                $entityManager->remove($redirect);
            }
        }
    }

    private function updateTargetRedirects(
        string $oldUrl,
        string $newUrl,
        EntityManager $entityManager
    ): void {
        $redirects = $entityManager->getRepository(Redirect::class)->findBy(
            [
                'target' => $oldUrl,
            ]
        );

        /** @var Redirect $redirect */
        foreach ($redirects as $redirect) {
            $redirect->setTarget($newUrl);
            $redirect->setIsAutoRedirect(true);
        }
    }

    private function createRedirect(
        EntityManager $entityManager,
        string $oldUrl,
        string $newUrl
    ): void {
        $redirect = new Redirect();
        $redirect->setOrigin($oldUrl);
        $redirect->setTarget($newUrl);
        $redirect->setPermanent(true);
        $redirect->setDomain($this->getDomain());
        $redirect->setIsAutoRedirect(true);

        $entityManager->persist($redirect);

        $entityManager->getUnitOfWork()->computeChangeSet(
            $entityManager->getClassMetadata(Redirect::class),
            $redirect
        );

        if (!isset($this->redirects[$oldUrl])) {
            $this->redirects[$oldUrl] = [];
        }

        $this->redirects[$oldUrl][] = $redirect;
    }

    private function getDomain(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return null;
        }

        return $request->getHost();
    }
}
