<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugEvent;
use Kunstmaan\NodeBundle\Helper\AutoSaverInterface;
use Kunstmaan\NodeBundle\Helper\NodeHelper;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AutoSaveController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $em;

    /** @var NodeHelper */
    private $nodeHelper;

    /** @var NodeMenu */
    private $nodeMenu;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var AutoSaverInterface */
    private $autoSaver;

    public function __construct(
        EntityManager $em,
        NodeHelper $nodeHelper,
        NodeMenu $nodeMenu,
        EventDispatcherInterface $eventDispatcher,
        AutoSaverInterface $autoSaver
    ) {
        $this->em = $em;
        $this->nodeHelper = $nodeHelper;
        $this->nodeMenu = $nodeMenu;
        $this->eventDispatcher = $eventDispatcher;
        $this->autoSaver = $autoSaver;
    }

    /**
     * @Route(
     *      "/{id}/auto-save",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_auto_save_node_verison",
     *      methods={"POST"}
     * )
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function autoSaveAction(Request $request, int $id)
    {
        $locale = $request->getLocale();
        /* @var HasNodeInterface $page */
        $page = null;
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);
        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $node);

        $nodeTranslation = $node->getNodeTranslation($locale, true);
        if (!$nodeTranslation) {
            return new NotFoundHttpException();
        }
        $publicVersion = $nodeTranslation->getPublicNodeVersion();
        if (!$publicVersion) {
            return new NotFoundHttpException();
        }
        $page = $publicVersion->getRef($this->em);
        $isStructureNode = $page->isStructureNode();
        /* no need to autosave structurenodes */
        if ($isStructureNode) {
            return new Response();
        }

        $nodeVersion = $this->nodeHelper->createAutoSaveVersion(
            $page,
            $nodeTranslation,
            $publicVersion
        );
        $page = $nodeVersion->getRef($this->em);
        $validAutoSave = $this->autoSaver->updateAutoSaveFromInput($page, $request, $node, $nodeTranslation, $isStructureNode, $nodeVersion);
        if ($validAutoSave) {
            return new Response();
        }

        $nodeMenu = $this->nodeMenu;
        $nodeMenu->setLocale($locale);
        $nodeMenu->setCurrentNode($node);
        $nodeMenu->setIncludeOffline(false);

        $renderContext = new RenderContext(
            [
                'nodetranslation' => $nodeTranslation,
                'slug' => $nodeTranslation->getSlug(),
                'page' => $page,
                'resource' => $page,
                'nodemenu' => $nodeMenu,
            ]
        );
        if (method_exists($page, 'getDefaultView')) {
            $renderContext->setView($page->getDefaultView());
        }
        $preEvent = new SlugEvent(null, $renderContext);
        $this->dispatch($preEvent, Events::PRE_SLUG_ACTION);
        $renderContext = $preEvent->getRenderContext();

        $postEvent = new SlugEvent(null, $renderContext);
        $this->dispatch($postEvent, Events::POST_SLUG_ACTION);

        $response = $postEvent->getResponse();
        $renderContext = $postEvent->getRenderContext();

        if ($response instanceof Response) {
            return $response;
        }

        $view = $renderContext->getView();
        if (empty($view)) {
            throw $this->createNotFoundException(sprintf('Missing view path for page "%s"', \get_class($page)));
        }

        $template = new Template([]);
        $template->setTemplate($view);
        $template->setOwner([SlugController::class, 'slugAction']);

        $request->attributes->set('_template', $template);

        return $renderContext->getArrayCopy();
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        $eventDispatcher = $this->eventDispatcher;
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $eventDispatcher->dispatch($eventName, $event);
    }
}
