<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\PageViewDataProviderInterface;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SlugController extends AbstractController
{
    /** @var NodeMenu */
    private $nodeMenu;
    /** @var PsrContainerInterface */
    private $viewDataProviderServiceLocator;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(NodeMenu $nodeMenu, PsrContainerInterface $viewDataProviderServiceLocator, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $em)
    {
        $this->nodeMenu = $nodeMenu;
        $this->viewDataProviderServiceLocator = $viewDataProviderServiceLocator;
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
    }

    /**
     * Handle the page requests
     *
     * @param Request $request The request
     * @param string  $url     The url
     * @param bool    $preview Show in preview mode
     *
     * @return Response|array
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function slugAction(Request $request, $url = null, $preview = false)
    {
        $locale = $request->getLocale();

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $request->attributes->get('_nodeTranslation');

        // If no node translation -> 404
        if (!$nodeTranslation) {
            throw $this->createNotFoundException('No page found for slug ' . $url);
        }

        $entity = $this->getPageEntity(
            $request,
            $preview,
            $this->em,
            $nodeTranslation
        );
        $node = $nodeTranslation->getNode();

        $securityEvent = new SlugSecurityEvent();
        $securityEvent
            ->setNode($node)
            ->setEntity($entity)
            ->setRequest($request)
            ->setNodeTranslation($nodeTranslation);

        $nodeMenu = $this->nodeMenu;
        $nodeMenu->setLocale($locale);
        $nodeMenu->setCurrentNode($node);
        $nodeMenu->setIncludeOffline($preview);

        $this->eventDispatcher->dispatch($securityEvent, Events::SLUG_SECURITY);

        // render page
        $renderContext = new RenderContext(
            [
                'nodetranslation' => $nodeTranslation,
                'slug' => $url,
                'page' => $entity,
                'resource' => $entity,
                'nodemenu' => $nodeMenu,
            ]
        );
        if (method_exists($entity, 'getDefaultView')) {
            $renderContext->setView($entity->getDefaultView());
        }

        $response = null;
        if ($entity instanceof CustomViewDataProviderInterface) {
            $serviceId = $entity->getViewDataProviderServiceId();

            if (!$this->viewDataProviderServiceLocator->has($serviceId)) {
                throw new \RuntimeException(sprintf('Missing page renderer service "%s"', $serviceId));
            }

            /** @var PageViewDataProviderInterface $service */
            $service = $this->viewDataProviderServiceLocator->get($serviceId);
            $service->provideViewData($nodeTranslation, $renderContext);

            $response = $renderContext->getResponse();
        }

        if ($response instanceof Response) {
            return $response;
        }

        $view = $renderContext->getView();
        if (empty($view)) {
            throw $this->createNotFoundException(sprintf('Missing view path for page "%s"', \get_class($entity)));
        }

        $template = new Template([]);
        $template->setTemplate($view);
        $template->setOwner([SlugController::class, 'slugAction']);

        $request->attributes->set('_template', $template);

        return $renderContext->getArrayCopy();
    }

    /**
     * @param bool $preview
     *
     * @return \Kunstmaan\NodeBundle\Entity\HasNodeInterface
     */
    private function getPageEntity(Request $request, $preview, EntityManagerInterface $em, NodeTranslation $nodeTranslation)
    {
        /* @var HasNodeInterface $entity */
        $entity = null;
        if ($preview) {
            $version = $request->query->get('version');
            if (!empty($version) && is_numeric($version)) {
                $nodeVersion = $em->getRepository(NodeVersion::class)->find($version);
                if (!\is_null($nodeVersion)) {
                    $entity = $nodeVersion->getRef($em);
                }
            }
        }
        if (\is_null($entity)) {
            $entity = $nodeTranslation->getPublicNodeVersion()->getRef($em);

            return $entity;
        }

        return $entity;
    }
}
