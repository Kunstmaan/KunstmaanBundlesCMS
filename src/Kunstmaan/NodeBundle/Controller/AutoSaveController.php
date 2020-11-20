<?php

namespace Kunstmaan\NodeBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPaneCreator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugEvent;
use Kunstmaan\NodeBundle\Form\NodeMenuTabAdminType;
use Kunstmaan\NodeBundle\Form\NodeMenuTabTranslationAdminType;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Kunstmaan\NodeBundle\Helper\NodeHelper;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class AutoSaveController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var NodeAdminPublisher
     */
    private $nodePublisher;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /** @var NodeHelper */
    private $nodeHelper;

    /** @var NodeMenu */
    private $nodeMenu;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var TabPaneCreator */
    private $tabPaneCreator;

    public function __construct(
        EntityManager $em,
        NodeAdminPublisher $nodePublisher,
        TranslatorInterface $translator,
        NodeHelper  $nodeHelper,
        NodeMenu $nodeMenu,
        EventDispatcherInterface  $eventDispatcher,
        TabPaneCreator $tabPaneCreator
    )
    {
        $this->em = $em;
        $this->nodePublisher = $nodePublisher;
        $this->translator = $translator;
        $this->nodeHelper = $nodeHelper;
        $this->nodeMenu = $nodeMenu;
        $this->eventDispatcher = $eventDispatcher;
        $this->tabPaneCreator = $tabPaneCreator;
    }

    /**
     * @Route(
     *      "/{id}/auto-save",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_auto_save_node_verison",
     *      methods={"POST"}
     * )
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
        /** no need to autosave structurenodes */
        if ($isStructureNode) {
            return new Response();
        }

        $this->nodeHelper->deletePreviousAutoSaves($page, $nodeTranslation);
        $nodeVersion = $this->nodeHelper->createAutoSaveVersion(
            $page,
            $nodeTranslation,
            $publicVersion
        );
        $page = $nodeVersion->getRef($this->em);
        $pagePartRefs = $this->em->getRepository(PagePartRef::class)->getPagePartRefs($page);
        $pagePartRefIds = array_reduce($pagePartRefs, function($array, $ref) {
            if($array === null){
                $array = [];
            }
            $array[] = $ref->getId();

            return $array;
        });

        $request->request->set('main_sequence', $pagePartRefIds);
        $form = $request->request->get('form');
        $form['main']['id'] = $page->getId();
        $formCopy = $form;
        foreach(array_keys($formCopy) as $key) {
            $matches = [];
            if(false !== strpos($key, 'pagepartadmin_')){
                $pagePartRef = reset($pagePartRefs);
                $newKey = 'pagepartadmin_'.$pagePartRef->getId();
                $form[$newKey] = $form[$key];
                unset($form[$key]);
                array_shift($pagePartRefs);
            }
        }
        $request->request->set('form', $form);
        unset($form);
        unset($formCopy);

        $tabPane = $this->tabPaneCreator->getDefaultTabPane(
            $request,
            $page,
            $node,
            $nodeTranslation,
            $isStructureNode,
            $nodeVersion
        );

        if (!$tabPane->isValid()) {
            return new Response();
        }

        $nodeVersion->setUpdated(new DateTime());
        $this->em->persist($nodeVersion);
        $this->em->flush();

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

        $template = new Template(array());
        $template->setTemplate($view);
        $template->setOwner([SlugController::class, 'slugAction']);

        $request->attributes->set('_template', $template);

        return $renderContext->getArrayCopy();
    }

    /**
     * @param object $event
     * @param string $eventName
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
