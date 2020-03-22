<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\PageRenderEvent;
use Kunstmaan\NodeBundle\Event\SlugEvent;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * This controller is for showing frontend pages based on slugs
 */
class SlugController extends Controller
{
    /**
     * Handle the page requests
     *
     * @param Request $request The request
     * @param string  $url     The url
     * @param bool    $preview Show in preview mode
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     *
     * @return Response|array
     */
    public function slugAction(Request $request, $url = null, $preview = false)
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
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
            $em,
            $nodeTranslation
        );
        $node = $nodeTranslation->getNode();

        $securityEvent = new SlugSecurityEvent();
        $securityEvent
            ->setNode($node)
            ->setEntity($entity)
            ->setRequest($request)
            ->setNodeTranslation($nodeTranslation);

        $nodeMenu = $this->container->get('kunstmaan_node.node_menu');
        $nodeMenu->setLocale($locale);
        $nodeMenu->setCurrentNode($node);
        $nodeMenu->setIncludeOffline($preview);

        $eventDispatcher = $this->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::SLUG_SECURITY, $securityEvent);

        //render page
        $renderContext = new RenderContext([
            'nodetranslation' => $nodeTranslation,
            'slug' => $url,
            'page' => $entity,
            'resource' => $entity,
            'nodemenu' => $nodeMenu,
        ]);

        if (method_exists($entity, 'getDefaultView')) {
            $renderContext->setView($entity->getDefaultView());
        }

        // NEXT_MAJOR: remove PRE_SLUG_ACTION dispatch
        $preEvent = new SlugEvent(null, $renderContext);
        $eventDispatcher->dispatch(Events::PRE_SLUG_ACTION, $preEvent);
        $renderContext = $preEvent->getRenderContext();

        /** @var PageInterface $entity */
        $deprecatedResponse = $entity->service($this->container, $request, $renderContext);
        // NEXT_MAJOR: end remove

        $pageRenderEvent = new PageRenderEvent($request, $entity, $renderContext);
        $eventDispatcher->dispatch(Events::PAGE_RENDER, $pageRenderEvent);

        $response = $pageRenderEvent->getResponse();
        $renderContext = clone $pageRenderEvent->getRenderContext();

        if ($response instanceof Response) {
            return $response;
        }

        // NEXT_MAJOR: remove POST_SLUG_ACTION dispatch
        $postEvent = new SlugEvent($deprecatedResponse, $renderContext);
        $eventDispatcher->dispatch(Events::POST_SLUG_ACTION, $postEvent);

        $response = $postEvent->getResponse();
        $renderContext = $postEvent->getRenderContext();

        if ($response instanceof Response) {
            return $response;
        }
        // NEXT_MAJOR: end remove

        $view = $renderContext->getView();
        if (empty($view)) {
            throw $this->createNotFoundException(sprintf('Missing view path for page "%s"', \get_class($entity)));
        }

        $template = new Template(array());
        $template->setTemplate($view);
        $template->setOwner([SlugController::class, 'slugAction']);

        $request->attributes->set('_template', $template);

        return $renderContext->getArrayCopy();
    }

    /**
     * @param Request                $request
     * @param bool                   $preview
     * @param EntityManagerInterface $em
     * @param NodeTranslation        $nodeTranslation
     *
     * @return \Kunstmaan\NodeBundle\Entity\HasNodeInterface
     */
    private function getPageEntity(Request $request, $preview, EntityManagerInterface $em, NodeTranslation $nodeTranslation)
    {
        /* @var HasNodeInterface $entity */
        $entity = null;
        if ($preview) {
            $version = $request->get('version');
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
