<?php

namespace Kunstmaan\NodeBundle\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminBundle\Service\AclManager;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\NodeBundle\AdminList\NodeAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeBundle\Event\CopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeBundle\Event\RecopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Event\RevertNodeAction;
use Kunstmaan\NodeBundle\Form\NodeMenuTabAdminType;
use Kunstmaan\NodeBundle\Form\NodeMenuTabTranslationAdminType;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeVersionLockHelper;
use Kunstmaan\NodeBundle\Helper\PageCloningHelper;
use Kunstmaan\NodeBundle\Repository\NodeVersionRepository;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * NodeAdminController
 */
class NodeAdminController extends Controller
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var BaseUser
     */
    protected $user;

    /**
     * @var AclHelper
     */
    protected $aclHelper;

    /**
     * @var AclManager
     */
    protected $aclManager;

    /**
     * @var NodeAdminPublisher
     */
    protected $nodePublisher;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /** @var PageCloningHelper */
    private $pageCloningHelper;

    /**
     * init
     */
    protected function init(Request $request)
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->locale = $request->getLocale();
        $this->authorizationChecker = $this->container->get('security.authorization_checker');
        $this->user = $this->getUser();
        $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
        $this->aclManager = $this->container->get('kunstmaan_admin.acl.manager');
        $this->nodePublisher = $this->container->get('kunstmaan_node.admin_node.publisher');
        $this->translator = $this->container->get('translator');
        $this->pageCloningHelper = $this->container->get(PageCloningHelper::class);
    }

    /**
     * @Route("/", name="KunstmaanNodeBundle_nodes")
     * @Template("@KunstmaanNode/Admin/list.html.twig")
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $this->init($request);

        $nodeAdminListConfigurator = new NodeAdminListConfigurator(
            $this->em,
            $this->aclHelper,
            $this->locale,
            PermissionMap::PERMISSION_VIEW,
            $this->authorizationChecker
        );

        $locale = $this->locale;
        $acl = $this->authorizationChecker;
        $itemRoute = function (EntityInterface $item) use ($locale, $acl) {
            if ($acl->isGranted(PermissionMap::PERMISSION_VIEW, $item->getNode())) {
                return [
                    'path' => '_slug_preview',
                    'params' => ['_locale' => $locale, 'url' => $item->getUrl()],
                ];
            }
        };
        $nodeAdminListConfigurator->addSimpleItemAction('action.preview', $itemRoute, 'eye');
        $nodeAdminListConfigurator->setDomainConfiguration($this->get('kunstmaan_admin.domain_configuration'));
        $nodeAdminListConfigurator->setShowAddHomepage($this->getParameter('kunstmaan_node.show_add_homepage') && $this->isGranted('ROLE_SUPER_ADMIN'));

        /** @var AdminList $adminlist */
        $adminlist = $this->get('kunstmaan_adminlist.factory')->createList($nodeAdminListConfigurator);
        $adminlist->bindRequest($request);

        return [
            'adminlist' => $adminlist,
        ];
    }

    /**
     * @Route(
     *      "/{id}/copyfromotherlanguage",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_copyfromotherlanguage",
     *      methods={"GET"}
     * )
     *
     * @param int $id The node id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function copyFromOtherLanguageAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $node);

        $originalLanguage = $request->get('originallanguage');
        $otherLanguageNodeTranslation = $node->getNodeTranslation($originalLanguage, true);
        $otherLanguageNodeNodeVersion = $otherLanguageNodeTranslation->getPublicNodeVersion();
        $otherLanguagePage = $otherLanguageNodeNodeVersion->getRef($this->em);
        $myLanguagePage = $this->get('kunstmaan_admin.clone.helper')
            ->deepCloneAndSave($otherLanguagePage);

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)
            ->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new CopyPageTranslationNodeEvent(
                $node,
                $nodeTranslation,
                $nodeVersion,
                $myLanguagePage,
                $otherLanguageNodeTranslation,
                $otherLanguageNodeNodeVersion,
                $otherLanguagePage,
                $originalLanguage
            ),
            Events::COPY_PAGE_TRANSLATION
        );

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', ['id' => $id]));
    }

    /**
     * @Route(
     *      "/{id}/recopyfromotherlanguage",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_recopyfromotherlanguage",
     *      methods={"POST"}
     * )
     *
     * @param int $id The node id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function recopyFromOtherLanguageAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $node);

        $otherLanguageNodeTranslation = $this->em->getRepository(NodeTranslation::class)->find($request->get('source'));
        $otherLanguageNodeNodeVersion = $otherLanguageNodeTranslation->getPublicNodeVersion();
        $otherLanguagePage = $otherLanguageNodeNodeVersion->getRef($this->em);
        $myLanguagePage = $this->get('kunstmaan_admin.clone.helper')
            ->deepCloneAndSave($otherLanguagePage);

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)
            ->addDraftNodeVersionFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new RecopyPageTranslationNodeEvent(
                $node,
                $nodeTranslation,
                $nodeVersion,
                $myLanguagePage,
                $otherLanguageNodeTranslation,
                $otherLanguageNodeNodeVersion,
                $otherLanguagePage,
                $otherLanguageNodeTranslation->getLang()
            ),
            Events::RECOPY_PAGE_TRANSLATION
        );

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', ['id' => $id, 'subaction' => NodeVersion::DRAFT_VERSION]));
    }

    /**
     * @Route(
     *      "/{id}/createemptypage",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_createemptypage",
     *      methods={"GET"}
     * )
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function createEmptyPageAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $node);

        $entityName = $node->getRefEntityName();
        /* @var HasNodeInterface $myLanguagePage */
        $myLanguagePage = new $entityName();
        $myLanguagePage->setTitle('New page');

        $this->em->persist($myLanguagePage);
        $this->em->flush();
        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)
            ->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $myLanguagePage),
            Events::ADD_EMPTY_PAGE_TRANSLATION
        );

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', ['id' => $id]));
    }

    /**
     * @Route("/{id}/publish", requirements={"id" =
     *                         "\d+"},
     *                         name="KunstmaanNodeBundle_nodes_publish", methods={"GET", "POST"})
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function publishAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $request = $this->get('request_stack')->getCurrentRequest();
        $this->nodePublisher->chooseHowToPublish($request, $nodeTranslation, $this->translator);

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', ['id' => $node->getId()]));
    }

    /**
     * @Route(
     *      "/{id}/unpublish",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_unpublish",
     *      methods={"GET", "POST"}
     * )
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function unPublishAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $request = $this->get('request_stack')->getCurrentRequest();
        $this->nodePublisher->chooseHowToUnpublish($request, $nodeTranslation, $this->translator);

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', ['id' => $node->getId()]));
    }

    /**
     * @Route(
     *      "/{id}/unschedulepublish",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_unschedule_publish",
     *      methods={"GET", "POST"}
     * )
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function unSchedulePublishAction(Request $request, $id)
    {
        $this->init($request);

        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $this->nodePublisher->unSchedulePublish($nodeTranslation);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->get('translator')->trans('kuma_node.admin.unschedule.flash.success')
        );

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', ['id' => $id]));
    }

    /**
     * @Route(
     *      "/{id}/delete",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_delete",
     *      methods={"POST"}
     * )
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function deleteAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_DELETE, $node);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->dispatch(
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
            Events::PRE_DELETE
        );

        $node->setDeleted(true);
        $this->em->persist($node);

        $children = $node->getChildren();
        $this->deleteNodeChildren($this->em, $this->user, $this->locale, $children);
        $this->em->flush();

        $event = new NodeEvent($node, $nodeTranslation, $nodeVersion, $page);
        $this->dispatch($event, Events::POST_DELETE);
        if (null === $response = $event->getResponse()) {
            $nodeParent = $node->getParent();
            // Check if we have a parent. Otherwise redirect to pages overview.
            if ($nodeParent) {
                $url = $this->get('router')->generate(
                    'KunstmaanNodeBundle_nodes_edit',
                    ['id' => $nodeParent->getId()]
                );
            } else {
                $url = $this->get('router')->generate(
                    'KunstmaanNodeBundle_nodes'
                );
            }
            $response = new RedirectResponse($url);
        }

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->get('translator')->trans('kuma_node.admin.delete.flash.success')
        );

        return $response;
    }

    /**
     * @Route(
     *      "/{id}/duplicate",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_duplicate",
     *      methods={"POST"}
     * )
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function duplicateAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $parentNode */
        $originalNode = $this->em->getRepository(Node::class)
            ->find($id);

        // Check with Acl
        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $originalNode);

        $request = $this->get('request_stack')->getCurrentRequest();

        $originalNodeTranslations = $originalNode->getNodeTranslation($this->locale, true);
        $originalRef = $originalNodeTranslations->getPublicNodeVersion()->getRef($this->em);
        $newPage = $this->get('kunstmaan_admin.clone.helper')
            ->deepCloneAndSave($originalRef);

        //set the title
        $title = $request->get('title');
        if (\is_string($title) && !empty($title)) {
            $newPage->setTitle($title);
        } else {
            $newPage->setTitle('New page');
        }

        //set the parent
        $parentNodeTranslation = $originalNode->getParent()->getNodeTranslation($this->locale, true);
        $parent = $parentNodeTranslation->getPublicNodeVersion()->getRef($this->em);
        $newPage->setParent($parent);
        $this->em->persist($newPage);
        $this->em->flush();

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository(Node::class)->createNodeFor(
            $newPage,
            $this->locale,
            $this->user
        );

        $nodeTranslation = $nodeNewPage->getNodeTranslation($this->locale, true);
        if ($newPage->isStructureNode()) {
            $nodeTranslation->setSlug('');
            $this->em->persist($nodeTranslation);
        }
        $this->em->flush();

        $this->aclManager->updateNodeAcl($originalNode, $nodeNewPage);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->get('translator')->trans('kuma_node.admin.duplicate.flash.success')
        );

        return $this->redirect(
            $this->generateUrl('KunstmaanNodeBundle_nodes_edit', ['id' => $nodeNewPage->getId()])
        );
    }

    /**
     * @Route(
     *      "/{id}/duplicate-with-children",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_duplicate_with_children",
     *      methods={"POST"}
     * )
     *
     * @throws AccessDeniedException
     */
    public function duplicateWithChildrenAction(Request $request, $id)
    {
        if (!$this->getParameter('kunstmaan_node.show_duplicate_with_children')) {
            return $this->redirectToRoute('KunstmaanNodeBundle_nodes_edit', ['id' => $id]);
        }

        $this->init($request);
        $title = $request->get('title', null);

        $nodeNewPage = $this->pageCloningHelper->duplicateWithChildren($id, $this->locale, $this->user, $title);

        $this->addFlash(FlashTypes::SUCCESS, $this->get('translator')->trans('kuma_node.admin.duplicate.flash.success'));

        return $this->redirectToRoute('KunstmaanNodeBundle_nodes_edit', ['id' => $nodeNewPage->getId()]);
    }

    /**
     * @Route(
     *      "/{id}/revert",
     *      requirements={"id" = "\d+"},
     *      defaults={"subaction" = "public"},
     *      name="KunstmaanNodeBundle_nodes_revert",
     *      methods={"GET"}
     * )
     *
     * @param int $id The node id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     */
    public function revertAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $node);

        $version = $request->get('version');

        if (empty($version) || !is_numeric($version)) {
            throw new InvalidArgumentException('No version was specified');
        }

        /* @var NodeVersionRepository $nodeVersionRepo */
        $nodeVersionRepo = $this->em->getRepository(NodeVersion::class);
        /* @var NodeVersion $nodeVersion */
        $nodeVersion = $nodeVersionRepo->find($version);

        if (\is_null($nodeVersion)) {
            throw new InvalidArgumentException('Version does not exist');
        }

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $page = $nodeVersion->getRef($this->em);
        /* @var HasNodeInterface $clonedPage */
        $clonedPage = $this->get('kunstmaan_admin.clone.helper')
            ->deepCloneAndSave($page);
        $newNodeVersion = $nodeVersionRepo->createNodeVersionFor(
            $clonedPage,
            $nodeTranslation,
            $this->user,
            $nodeVersion,
            'draft'
        );

        $nodeTranslation->setTitle($clonedPage->getTitle());
        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->dispatch(
            new RevertNodeAction(
                $node,
                $nodeTranslation,
                $newNodeVersion,
                $clonedPage,
                $nodeVersion,
                $page
            ),
            Events::REVERT
        );

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->get('translator')->trans('kuma_node.admin.revert.flash.success')
        );

        return $this->redirect(
            $this->generateUrl(
                'KunstmaanNodeBundle_nodes_edit',
                [
                    'id' => $id,
                    'subaction' => 'draft',
                ]
            )
        );
    }

    /**
     * @Route(
     *      "/{id}/add",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_add",
     *      methods={"POST"}
     * )
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     */
    public function addAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $parentNode */
        $parentNode = $this->em->getRepository(Node::class)->find($id);

        // Check with Acl
        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $parentNode);

        $parentNodeTranslation = $parentNode->getNodeTranslation($this->locale, true);
        $parentNodeVersion = $parentNodeTranslation->getPublicNodeVersion();
        $parentPage = $parentNodeVersion->getRef($this->em);

        $type = $this->validatePageType($request);
        $newPage = $this->createNewPage($request, $type);
        $newPage->setParent($parentPage);

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository(Node::class)
            ->createNodeFor($newPage, $this->locale, $this->user);
        $nodeTranslation = $nodeNewPage->getNodeTranslation(
            $this->locale,
            true
        );
        $weight = $this->em->getRepository(NodeTranslation::class)
                ->getMaxChildrenWeight($parentNode, $this->locale) + 1;
        $nodeTranslation->setWeight($weight);

        if ($newPage->isStructureNode()) {
            $nodeTranslation->setSlug('');
        }

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->aclManager->updateNodeAcl($parentNode, $nodeNewPage);

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new NodeEvent(
                $nodeNewPage, $nodeTranslation, $nodeVersion, $newPage
            ),
            Events::ADD_NODE
        );

        return $this->redirect(
            $this->generateUrl(
                'KunstmaanNodeBundle_nodes_edit',
                ['id' => $nodeNewPage->getId()]
            )
        );
    }

    /**
     * @Route("/add-homepage", name="KunstmaanNodeBundle_nodes_add_homepage", methods={"POST"})
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     */
    public function addHomepageAction(Request $request)
    {
        $this->init($request);

        // Check with Acl
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $type = $this->validatePageType($request);

        $newPage = $this->createNewPage($request, $type);

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository(Node::class)
            ->createNodeFor($newPage, $this->locale, $this->user);
        $nodeTranslation = $nodeNewPage->getNodeTranslation(
            $this->locale,
            true
        );
        $this->em->flush();

        // Set default permissions
        $this->container->get('kunstmaan_node.acl_permission_creator_service')
            ->createPermission($nodeNewPage);

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->dispatch(
            new NodeEvent(
                $nodeNewPage, $nodeTranslation, $nodeVersion, $newPage
            ),
            Events::ADD_NODE
        );

        return $this->redirect(
            $this->generateUrl(
                'KunstmaanNodeBundle_nodes_edit',
                ['id' => $nodeNewPage->getId()]
            )
        );
    }

    /**
     * @Route("/reorder", name="KunstmaanNodeBundle_nodes_reorder", methods={"POST"})
     *
     * @return string
     *
     * @throws AccessDeniedException
     */
    public function reorderAction(Request $request)
    {
        $this->init($request);
        $nodes = [];
        $nodeIds = $request->get('nodes');
        $changeParents = $request->get('parent');

        foreach ($nodeIds as $id) {
            /* @var Node $node */
            $node = $this->em->getRepository(Node::class)->find($id);
            $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $node);
            $nodes[] = $node;
        }

        $weight = 1;
        foreach ($nodes as $node) {
            $newParentId = isset($changeParents[$node->getId()]) ? $changeParents[$node->getId()] : null;
            if ($newParentId) {
                $parent = $this->em->getRepository(Node::class)->find($newParentId);
                $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $parent);
                $node->setParent($parent);
                $this->em->persist($node);
                $this->em->flush($node);
            }

            /* @var NodeTranslation $nodeTranslation */
            $nodeTranslation = $node->getNodeTranslation($this->locale, true);

            if ($nodeTranslation) {
                $nodeVersion = $nodeTranslation->getPublicNodeVersion();
                $page = $nodeVersion->getRef($this->em);

                $this->dispatch(
                    new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
                    Events::PRE_PERSIST
                );

                $nodeTranslation->setWeight($weight);
                $this->em->persist($nodeTranslation);
                $this->em->flush($nodeTranslation);

                $this->dispatch(
                    new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
                    Events::POST_PERSIST
                );

                ++$weight;
            }
        }

        return new JsonResponse(
            [
                'Success' => 'The node-translations for [' . $this->locale . '] have got new weight values',
            ]
        );
    }

    /**
     * @Route(
     *      "/{id}/{subaction}",
     *      requirements={"id" = "\d+"},
     *      defaults={"subaction" = "public"},
     *      name="KunstmaanNodeBundle_nodes_edit",
     *      methods={"GET", "POST"}
     * )
     * @Template("@KunstmaanNode/NodeAdmin/edit.html.twig")
     *
     * @param int    $id        The node id
     * @param string $subaction The subaction (draft|public)
     *
     * @return RedirectResponse|array
     *
     * @throws AccessDeniedException
     */
    public function editAction(Request $request, $id, $subaction)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_EDIT, $node);

        $tabPane = new TabPane(
            'todo',
            $request,
            $this->container->get('form.factory')
        );

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        if (!$nodeTranslation) {
            return $this->renderNodeNotTranslatedPage($node);
        }

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $draftNodeVersion = $nodeTranslation->getDraftNodeVersion();
        $nodeVersionIsLocked = false;

        /* @var HasNodeInterface $page */
        $page = null;
        $draft = ($subaction == 'draft');
        $saveAsDraft = $request->get('saveasdraft');
        if ((!$draft && !empty($saveAsDraft)) || ($draft && \is_null($draftNodeVersion))) {
            // Create a new draft version
            $draft = true;
            $subaction = 'draft';
            $page = $nodeVersion->getRef($this->em);
            $nodeVersion = $this->createDraftVersion(
                $page,
                $nodeTranslation,
                $nodeVersion
            );
            $draftNodeVersion = $nodeVersion;
        } elseif ($draft) {
            $nodeVersion = $draftNodeVersion;
            $page = $nodeVersion->getRef($this->em);
        } else {
            if ($request->getMethod() == 'POST') {
                $nodeVersionIsLocked = $this->isNodeVersionLocked($nodeTranslation, true);

                //Check the version timeout and make a new nodeversion if the timeout is passed
                $thresholdDate = date(
                    'Y-m-d H:i:s',
                    time() - $this->getParameter(
                        'kunstmaan_node.version_timeout'
                    )
                );
                $updatedDate = date(
                    'Y-m-d H:i:s',
                    strtotime($nodeVersion->getUpdated()->format('Y-m-d H:i:s'))
                );
                if ($thresholdDate >= $updatedDate || $nodeVersionIsLocked) {
                    $page = $nodeVersion->getRef($this->em);
                    if ($nodeVersion === $nodeTranslation->getPublicNodeVersion()) {
                        $this->nodePublisher
                            ->createPublicVersion(
                                $page,
                                $nodeTranslation,
                                $nodeVersion,
                                $this->user
                            );
                    } else {
                        $this->createDraftVersion(
                            $page,
                            $nodeTranslation,
                            $nodeVersion
                        );
                    }
                }
            }
            $page = $nodeVersion->getRef($this->em);
        }
        $isStructureNode = $page->isStructureNode();

        $menubuilder = $this->get('kunstmaan_node.actions_menu_builder');
        $menubuilder->setActiveNodeVersion($nodeVersion);
        $menubuilder->setEditableNode(!$isStructureNode);

        // Building the form
        $propertiesWidget = new FormWidget();
        $propertiesWidget->addType('main', $page->getDefaultAdminType(), $page);
        $propertiesWidget->addType('node', $node->getDefaultAdminType(), $node);
        $tabPane->addTab(new Tab('kuma_node.tab.properties.title', $propertiesWidget));

        // Menu tab
        $menuWidget = new FormWidget();
        $menuWidget->addType(
            'menunodetranslation',
            NodeMenuTabTranslationAdminType::class,
            $nodeTranslation,
            ['slugable' => !$isStructureNode]
        );
        $menuWidget->addType('menunode', NodeMenuTabAdminType::class, $node, ['available_in_nav' => !$isStructureNode]);
        $tabPane->addTab(new Tab('kuma_node.tab.menu.title', $menuWidget));

        $this->dispatch(
            new AdaptFormEvent(
                $request,
                $tabPane,
                $page,
                $node,
                $nodeTranslation,
                $nodeVersion
            ),
            Events::ADAPT_FORM
        );

        $tabPane->buildForm();

        if ($request->getMethod() == 'POST') {
            $tabPane->bindRequest($request);

            // Don't redirect to listing when coming from ajax request, needed for url chooser.
            if ($tabPane->isValid() && !$request->isXmlHttpRequest()) {
                $this->dispatch(
                    new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
                    Events::PRE_PERSIST
                );

                $nodeTranslation->setTitle($page->getTitle());
                if ($isStructureNode) {
                    $nodeTranslation->setSlug('');
                }
                $nodeVersion->setUpdated(new DateTime());
                if ($nodeVersion->getType() == 'public') {
                    $nodeTranslation->setUpdated($nodeVersion->getUpdated());
                }
                $this->em->persist($nodeTranslation);
                $this->em->persist($nodeVersion);
                $tabPane->persist($this->em);
                $this->em->flush();

                $this->dispatch(
                    new NodeEvent($node, $nodeTranslation, $nodeVersion, $page),
                    Events::POST_PERSIST
                );

                if ($nodeVersionIsLocked) {
                    $this->addFlash(
                        FlashTypes::SUCCESS,
                        $this->get('translator')->trans('kuma_node.admin.edit.flash.locked_success')
                    );
                } elseif ($request->request->has('publishing') || $request->request->has('publish_later')) {
                    $this->nodePublisher->chooseHowToPublish($request, $nodeTranslation, $this->translator);
                } elseif ($request->request->has('unpublishing') || $request->request->has('unpublish_later')) {
                    $this->nodePublisher->chooseHowToUnpublish($request, $nodeTranslation, $this->translator);
                } else {
                    $this->addFlash(
                        FlashTypes::SUCCESS,
                        $this->get('translator')->trans('kuma_node.admin.edit.flash.success')
                    );
                }

                $params = [
                    'id' => $node->getId(),
                    'subaction' => $subaction,
                    'currenttab' => $tabPane->getActiveTab(),
                ];
                $params = array_merge(
                    $params,
                    $tabPane->getExtraParams($request)
                );

                if ($subaction === 'draft' && ($request->request->has('publishing') || $request->request->has('publish_later'))) {
                    unset($params['subaction']);
                }

                return $this->redirect(
                    $this->generateUrl(
                        'KunstmaanNodeBundle_nodes_edit',
                        $params
                    )
                );
            }
        }

        $nodeVersions = $this->em->getRepository(NodeVersion::class)->findBy(
            ['nodeTranslation' => $nodeTranslation],
            ['updated' => 'DESC']
        );
        $queuedNodeTranslationAction = $this->em->getRepository(QueuedNodeTranslationAction::class)->findOneBy(['nodeTranslation' => $nodeTranslation]);

        return [
            'page' => $page,
            'entityname' => ClassLookup::getClass($page),
            'nodeVersions' => $nodeVersions,
            'node' => $node,
            'nodeTranslation' => $nodeTranslation,
            'draft' => $draft,
            'draftNodeVersion' => $draftNodeVersion,
            'showDuplicateWithChildren' => $this->getParameter('kunstmaan_node.show_duplicate_with_children'),
            'nodeVersion' => $nodeVersion,
            'subaction' => $subaction,
            'tabPane' => $tabPane,
            'editmode' => true,
            'childCount' => $this->em->getRepository('KunstmaanNodeBundle:Node')->getChildCount($node),
            'queuedNodeTranslationAction' => $queuedNodeTranslationAction,
            'nodeVersionLockCheck' => $this->container->getParameter('kunstmaan_node.lock_enabled'),
            'nodeVersionLockInterval' => $this->container->getParameter('kunstmaan_node.lock_check_interval'),
        ];
    }

    /**
     * @Route(
     *      "checkNodeVersionLock/{id}/{public}",
     *      requirements={"id" = "\d+", "public" = "(0|1)"},
     *      name="KunstmaanNodeBundle_nodes_versionlock_check"
     * )
     *
     * @return JsonResponse
     */
    public function checkNodeVersionLockAction(Request $request, $id, $public)
    {
        $nodeVersionIsLocked = false;
        $message = '';
        $this->init($request);

        /* @var Node $node */
        $node = $this->em->getRepository(Node::class)->find($id);

        try {
            $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

            /** @var NodeVersionLockHelper $nodeVersionLockHelper */
            $nodeVersionLockHelper = $this->get('kunstmaan_node.admin_node.node_version_lock_helper');
            $nodeTranslation = $node->getNodeTranslation($this->locale, true);

            if ($nodeTranslation) {
                $nodeVersionIsLocked = $nodeVersionLockHelper->isNodeVersionLocked($this->getUser(), $nodeTranslation, $public);

                if ($nodeVersionIsLocked) {
                    $users = $nodeVersionLockHelper->getUsersWithNodeVersionLock($nodeTranslation, $public, $this->getUser());
                    $message = $this->get('translator')->trans('kuma_node.admin.edit.flash.locked', ['%users%' => implode(', ', $users)]);
                }
            }
        } catch (AccessDeniedException $ade) {
        }

        return new JsonResponse(['lock' => $nodeVersionIsLocked, 'message' => $message]);
    }

    /**
     * @param bool $isPublic
     *
     * @return bool
     */
    private function isNodeVersionLocked(NodeTranslation $nodeTranslation, $isPublic)
    {
        if ($this->container->getParameter('kunstmaan_node.lock_enabled')) {
            /** @var NodeVersionLockHelper $nodeVersionLockHelper */
            $nodeVersionLockHelper = $this->get('kunstmaan_node.admin_node.node_version_lock_helper');
            $nodeVersionIsLocked = $nodeVersionLockHelper->isNodeVersionLocked($this->getUser(), $nodeTranslation, $isPublic);

            return $nodeVersionIsLocked;
        }

        return false;
    }

    /**
     * @param HasNodeInterface $page            The page
     * @param NodeTranslation  $nodeTranslation The node translation
     * @param NodeVersion      $nodeVersion     The node version
     *
     * @return NodeVersion
     */
    private function createDraftVersion(
        HasNodeInterface $page,
        NodeTranslation $nodeTranslation,
        NodeVersion $nodeVersion
    ) {
        $publicPage = $this->get('kunstmaan_admin.clone.helper')
            ->deepCloneAndSave($page);
        /* @var NodeVersion $publicNodeVersion */

        $publicNodeVersion = $this->em->getRepository(
            NodeVersion::class
        )->createNodeVersionFor(
            $publicPage,
            $nodeTranslation,
            $this->user,
            $nodeVersion->getOrigin(),
            'public',
            $nodeVersion->getCreated()
        );

        $nodeTranslation->setPublicNodeVersion($publicNodeVersion);
        $nodeVersion->setType('draft');
        $nodeVersion->setOrigin($publicNodeVersion);
        $nodeVersion->setCreated(new DateTime());

        $this->em->persist($nodeTranslation);
        $this->em->persist($nodeVersion);
        $this->em->flush();

        $this->dispatch(
            new NodeEvent(
                $nodeTranslation->getNode(),
                $nodeTranslation,
                $nodeVersion,
                $page
            ),
            Events::CREATE_DRAFT_VERSION
        );

        return $nodeVersion;
    }

    /**
     * @param Node   $node       The node
     * @param string $permission The permission to check for
     *
     * @throws AccessDeniedException
     */
    private function checkPermission(Node $node, $permission)
    {
        if (false === $this->authorizationChecker->isGranted($permission, $node)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param EntityManager   $em       The Entity Manager
     * @param BaseUser        $user     The user who deletes the children
     * @param string          $locale   The locale that was used
     * @param ArrayCollection $children The children array
     */
    private function deleteNodeChildren(
        EntityManager $em,
        BaseUser $user,
        $locale,
        ArrayCollection $children
    ) {
        /* @var Node $childNode */
        foreach ($children as $childNode) {
            $childNodeTranslation = $childNode->getNodeTranslation(
                $this->locale,
                true
            );

            $childNodeVersion = $childNodeTranslation->getPublicNodeVersion();
            $childNodePage = $childNodeVersion->getRef($this->em);

            $this->dispatch(
                new NodeEvent(
                    $childNode,
                    $childNodeTranslation,
                    $childNodeVersion,
                    $childNodePage
                ),
                Events::PRE_DELETE
            );

            $childNode->setDeleted(true);
            $this->em->persist($childNode);

            $children2 = $childNode->getChildren();
            $this->deleteNodeChildren($em, $user, $locale, $children2);

            $this->dispatch(
                new NodeEvent(
                    $childNode,
                    $childNodeTranslation,
                    $childNodeVersion,
                    $childNodePage
                ),
                Events::POST_DELETE
            );
        }
    }

    /**
     * @param string $type
     *
     * @return HasNodeInterface
     */
    private function createNewPage(Request $request, $type)
    {
        /* @var HasNodeInterface $newPage */
        $newPage = new $type();

        $title = $request->get('title');
        if (\is_string($title) && !empty($title)) {
            $newPage->setTitle($title);
        } else {
            $newPage->setTitle($this->get('translator')->trans('kuma_node.admin.new_page.title.default'));
        }
        $this->em->persist($newPage);
        $this->em->flush();

        return $newPage;
    }

    /**
     * @param Request $request
     *
     * @return string
     * @throw InvalidArgumentException
     */
    private function validatePageType($request)
    {
        $type = $request->get('type');

        if (empty($type)) {
            throw new InvalidArgumentException('Please specify a type of page you want to create');
        }

        return $type;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderNodeNotTranslatedPage(Node $node)
    {
        //try to find a parent node with the correct translation, if there is none allow copy.
        //if there is a parent but it doesn't have the language to copy to don't allow it
        $parentNode = $node->getParent();
        if ($parentNode) {
            $parentNodeTranslation = $parentNode->getNodeTranslation(
                $this->locale,
                true
            );
            $parentsAreOk = false;

            if ($parentNodeTranslation) {
                $parentsAreOk = $this->em->getRepository(
                    NodeTranslation::class
                )->hasParentNodeTranslationsForLanguage(
                    $node->getParent()->getNodeTranslation(
                        $this->locale,
                        true
                    ),
                    $this->locale
                );
            }
        } else {
            $parentsAreOk = true;
        }

        return $this->render(
            '@KunstmaanNode/NodeAdmin/pagenottranslated.html.twig',
            [
                'node' => $node,
                'nodeTranslations' => $node->getNodeTranslations(
                    true
                ),
                'copyfromotherlanguages' => $parentsAreOk,
            ]
        );
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        $eventDispatcher = $this->container->get('event_dispatcher');
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $eventDispatcher->dispatch($eventName, $event);
    }
}
