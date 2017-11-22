<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugEvent;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\NodeMenuItem;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * This controller is for showing frontend pages based on slugs
 */
class SlugController extends Controller
{
    /** @var EventDispatcher $eventDispatcher */
    protected $eventDispatcher;

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
        $em              = $this->getDoctrine()->getManager();
        $locale          = $request->getLocale();
        $nodeTranslation = $this->getNodeTranslation($request, $url);
        $entity          = $this->getPageEntity($request, $preview, $em, $nodeTranslation);
        $node            = $nodeTranslation->getNode();
        $securityEvent   = $this->getSecurityEvent($node, $entity, $request, $nodeTranslation);
        $nodeMenu        = $this->getNodeMenu($locale, $node, $preview);
        $renderContext   = $this->getRenderContext($nodeTranslation, $url, $entity, $nodeMenu, $securityEvent);
        /** @noinspection PhpUndefinedMethodInspection */
        $response        = $entity->service($this->container, $request, $renderContext);
        $postEvent       = new SlugEvent($response, $renderContext);
        $this->eventDispatcher->dispatch(Events::POST_SLUG_ACTION, $postEvent);
        $response        = $postEvent->getResponse();
        $renderContext   = $postEvent->getRenderContext();

        return $this->returnResult($request, $url, $renderContext, $response);
    }

    /**
     * @param Request $request
     * @param $url
     * @return NodeTranslation
     */
    private function getNodeTranslation(Request $request, $url)
    {
        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $request->attributes->get('_nodeTranslation');

        // If no node translation -> 404
        if (!$nodeTranslation) {
            throw $this->createNotFoundException('No page found for slug ' . $url);
        }

        return $nodeTranslation;
    }

    /**
     * @param Node $node
     * @param EntityInterface $entity
     * @param Request $request
     * @param NodeTranslation $nodeTranslation
     * @return SlugSecurityEvent
     */
    private function getSecurityEvent(Node $node, EntityInterface $entity, Request $request, NodeTranslation $nodeTranslation)
    {
        $securityEvent = new SlugSecurityEvent();
        $securityEvent
            ->setNode($node)
            ->setEntity($entity)
            ->setRequest($request)
            ->setNodeTranslation($nodeTranslation);

        return $securityEvent;
    }

    /**
     * @param $locale
     * @param Node $node
     * @param bool $preview
     *
     * @return NodeMenu
     */
    private function getNodeMenu($locale, Node $node, $preview)
    {
        /** @var NodeMenu $nodeMenu */
        $nodeMenu = $this->container->get('kunstmaan_node.node_menu');
        $nodeMenu->setLocale($locale);
        $nodeMenu->setCurrentNode($node);
        $nodeMenu->setIncludeOffline($preview);

        return $nodeMenu;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     * @param $url
     * @param EntityInterface $entity
     * @param NodeMenu $nodeMenu
     * @param SlugSecurityEvent $securityEvent
     * @return RenderContext
     */
    private function getRenderContext(NodeTranslation $nodeTranslation, $url, EntityInterface $entity, NodeMenu $nodeMenu, SlugSecurityEvent $securityEvent)
    {
        $this->eventDispatcher = $this->get('event_dispatcher');
        $this->eventDispatcher->dispatch(Events::SLUG_SECURITY, $securityEvent);

        $renderContext = new RenderContext([
            'nodetranslation' => $nodeTranslation,
            'slug'            => $url,
            'page'            => $entity,
            'resource'        => $entity,
            'nodemenu'        => $nodeMenu,
        ]);

        if (method_exists($entity, 'getDefaultView')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $renderContext->setView($entity->getDefaultView());
        }

        $preEvent = new SlugEvent(null, $renderContext);
        $this->eventDispatcher->dispatch(Events::PRE_SLUG_ACTION, $preEvent);
        $renderContext = $preEvent->getRenderContext();

        return $renderContext;
    }

    /**
     * @param Request $request
     * @param Response|null $response
     * @param $url
     * @param RenderContext $renderContext
     * @return array|Response
     */
    private function returnResult(Request $request, $url, RenderContext $renderContext, Response $response = null)
    {
        if ($response instanceof Response) {
            return $response;
        }

        $view = $renderContext->getView();
        if (empty($view)) {
            throw $this->createNotFoundException('No page found for slug ' . $url);
        }

        $template = new Template(array());
        $template->setTemplate($view);
        $request->attributes->set('_template', $template);

        return $renderContext->getArrayCopy();
    }

    /**
     * @param Request                $request
     * @param boolean                $preview
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
                $nodeVersion = $em->getRepository('KunstmaanNodeBundle:NodeVersion')->find($version);
                if (!is_null($nodeVersion)) {
                    $entity = $nodeVersion->getRef($em);
                }
            }
        }
        if (is_null($entity)) {
            $entity = $nodeTranslation->getPublicNodeVersion()->getRef($em);

            return $entity;
        }

        return $entity;
    }
}
