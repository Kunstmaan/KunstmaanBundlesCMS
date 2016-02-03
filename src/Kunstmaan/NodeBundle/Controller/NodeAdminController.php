<?php

namespace Kunstmaan\NodeBundle\Controller;

use DateTime;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\NodeBundle\Form\NodeMenuTabTranslationAdminType;
use Kunstmaan\NodeBundle\Form\NodeMenuTabAdminType;
use InvalidArgumentException;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\NodeBundle\AdminList\NodeAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeBundle\Event\RevertNodeAction;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\NodeBundle\Repository\NodeVersionRepository;
use Kunstmaan\NodeBundle\Event\CopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;

/**
 * NodeAdminController
 */
class NodeAdminController extends Controller
{
    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var string $locale
     */
    protected $locale;

    /**
     * @var AuthorizationCheckerInterface $authorizationChecker
     */
    protected $authorizationChecker;

    /**
     * @var BaseUser $user
     */
    protected $user;

    /**
     * @var AclHelper $aclHelper
     */
    protected $aclHelper;


    /**
     * init
     *
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->em                   = $this->getDoctrine()->getManager();
        $this->locale               = $request->getLocale();
        $this->authorizationChecker = $this->get('security.authorization_checker');
        $this->user                 = $this->getUser();
        $this->aclHelper            = $this->get('kunstmaan_admin.acl.helper');
    }

    /**
     * @Route("/", name="KunstmaanNodeBundle_nodes")
     * @Template("KunstmaanNodeBundle:Admin:list.html.twig")
     *
     * @param Request $request
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
            PermissionMap::PERMISSION_EDIT
        );
        $nodeAdminListConfigurator->setDomainConfiguration($this->get('kunstmaan_admin.domain_configuration'));
        $nodeAdminListConfigurator->setShowAddHomepage($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN'));

        /** @var AdminList $adminlist */
        $adminlist = $this->get('kunstmaan_adminlist.factory')->createList($nodeAdminListConfigurator);
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
        );
    }

    /**
     * @Route(
     *      "/{id}/copyfromotherlanguage",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_copyfromotherlanguage"
     * )
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @param int     $id The node id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     */
    public function copyFromOtherLanguageAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $originalLanguage             = $request->get('originallanguage');
        $otherLanguageNodeTranslation = $node->getNodeTranslation($originalLanguage, true);
        $otherLanguageNodeNodeVersion = $otherLanguageNodeTranslation->getPublicNodeVersion();
        $otherLanguagePage            = $otherLanguageNodeNodeVersion->getRef($this->em);
        $myLanguagePage               = $this->get('kunstmaan_admin.clone.helper')
            ->deepCloneAndSave($otherLanguagePage);

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')
            ->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion     = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(
            Events::COPY_PAGE_TRANSLATION,
            new CopyPageTranslationNodeEvent(
                $node,
                $nodeTranslation,
                $nodeVersion,
                $myLanguagePage,
                $otherLanguageNodeTranslation,
                $otherLanguageNodeNodeVersion,
                $otherLanguagePage,
                $originalLanguage
            )
        );

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $id)));
    }

    /**
     * @Route(
     *      "/{id}/createemptypage",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_createemptypage"
     * )
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     */
    public function createEmptyPageAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $entityName = $node->getRefEntityName();
        /* @var HasNodeInterface $myLanguagePage */
        $myLanguagePage = new $entityName();
        $myLanguagePage->setTitle('New page');

        $this->em->persist($myLanguagePage);
        $this->em->flush();
        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')
            ->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion     = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(
            Events::ADD_EMPTY_PAGE_TRANSLATION,
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $myLanguagePage)
        );

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $id)));
    }

    /**
     * @Route("/{id}/publish", requirements={"id" =
     *                         "\d+"},
     *                         name="KunstmaanNodeBundle_nodes_publish")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     */
    public function publishAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $request         = $this->get('request');

        if ($request->get('pub_date')) {
            $date = new \DateTime(
                $request->get('pub_date') . ' ' . $request->get('pub_time')
            );
            $this->get('kunstmaan_node.admin_node.publisher')->publishLater(
                $nodeTranslation,
                $date
            );
            $this->get('session')->getFlashBag()->add(
                'success',
                'Publishing of the page has been scheduled'
            );
        } else {
            $this->get('kunstmaan_node.admin_node.publisher')->publish(
                $nodeTranslation
            );
            $this->get('session')->getFlashBag()->add(
                'success',
                'The page has been published'
            );
        }

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $node->getId())));
    }

    /**
     * @Route(
     *      "/{id}/unpublish",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_unpublish"
     * )
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     */
    public function unPublishAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $request         = $this->get('request');

        if ($request->get('unpub_date')) {
            $date = new \DateTime($request->get('unpub_date') . ' ' . $request->get('unpub_time'));
            $this->get('kunstmaan_node.admin_node.publisher')->unPublishLater($nodeTranslation, $date);
            $this->get('session')->getFlashBag()->add('success', 'Unpublishing of the page has been scheduled');
        } else {
            $this->get('kunstmaan_node.admin_node.publisher')->unPublish($nodeTranslation);
            $this->get('session')->getFlashBag()->add('success', 'The page has been unpublished');
        }

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $node->getId())));
    }

    /**
     * @Route(
     *      "/{id}/unschedulepublish",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_unschedule_publish"
     * )
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     */
    public function unSchedulePublishAction(Request $request, $id)
    {
        $this->init($request);

        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $this->get('kunstmaan_node.admin_node.publisher')->unSchedulePublish($nodeTranslation);

        $this->get('session')->getFlashBag()->add('success', 'The scheduling has been canceled');

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $id)));
    }

    /**
     * @Route(
     *      "/{id}/delete",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_delete"
     * )
     * @Template()
     * @Method("POST")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     */
    public function deleteAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_DELETE);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeVersion     = $nodeTranslation->getPublicNodeVersion();
        $page            = $nodeVersion->getRef($this->em);

        $this->get('event_dispatcher')->dispatch(
            Events::PRE_DELETE,
            new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
        );

        $node->setDeleted(true);
        $this->em->persist($node);

        $children = $node->getChildren();
        $this->deleteNodeChildren($this->em, $this->user, $this->locale, $children);
        $this->em->flush();

        $event = new NodeEvent($node, $nodeTranslation, $nodeVersion, $page);
        $this->get('event_dispatcher')->dispatch(Events::POST_DELETE, $event);
        if (null === $response = $event->getResponse()) {
            $nodeParent = $node->getParent();
            $url        = $this->get('router')->generate(
                'KunstmaanNodeBundle_nodes_edit',
                array('id' => $nodeParent->getId())
            );
            $response   = new RedirectResponse($url);
        }

        $this->get('session')->getFlashBag()->add('success', 'The page is deleted!');

        return $response;
    }

    /**
     * @Route(
     *      "/{id}/duplicate",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_duplicate"
     * )
     * @Template()
     * @Method("POST")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     */
    public function duplicateAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $parentNode */
        $originalNode = $this->em->getRepository('KunstmaanNodeBundle:Node')
            ->find($id);

        // Check with Acl
        $this->checkPermission($originalNode, PermissionMap::PERMISSION_EDIT);

        $request = $this->get('request');

        $originalNodeTranslations = $originalNode->getNodeTranslation($this->locale, true);
        $originalRef              = $originalNodeTranslations->getPublicNodeVersion()->getRef($this->em);
        $newPage                  = $this->get('kunstmaan_admin.clone.helper')
            ->deepCloneAndSave($originalRef);

        //set the title
        $title = $request->get('title');
        if (is_string($title) && !empty($title)) {
            $newPage->setTitle($title);
        } else {
            $newPage->setTitle('New page');
        }

        //set the parent
        $parentNodeTranslation = $originalNode->getParent()->getNodeTranslation($this->locale, true);
        $parent                = $parentNodeTranslation->getPublicNodeVersion()->getRef($this->em);
        $newPage->setParent($parent);
        $this->em->persist($newPage);
        $this->em->flush();

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository('KunstmaanNodeBundle:Node')->createNodeFor(
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

        $this->updateAcl($originalNode, $nodeNewPage);

        $this->get('session')->getFlashBag()->add('success', 'The page has been duplicated!');

        return $this->redirect(
            $this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $nodeNewPage->getId()))
        );
    }

    /**
     * @Route(
     *      "/{id}/revert",
     *      requirements={"id" = "\d+"},
     *      defaults={"subaction" = "public"},
     *      name="KunstmaanNodeBundle_nodes_revert"
     * )
     * @Template()
     * @Method("GET")
     *
     * @param Request $request
     * @param int     $id The node id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     */
    public function revertAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $version = $request->get('version');

        if (empty($version) || !is_numeric($version)) {
            throw new InvalidArgumentException('No version was specified');
        }

        /* @var NodeVersionRepository $nodeVersionRepo */
        $nodeVersionRepo = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion');
        /* @var NodeVersion $nodeVersion */
        $nodeVersion = $nodeVersionRepo->find($version);

        if (is_null($nodeVersion)) {
            throw new InvalidArgumentException('Version does not exist');
        }

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $page            = $nodeVersion->getRef($this->em);
        /* @var HasNodeInterface $clonedPage */
        $clonedPage     = $this->get('kunstmaan_admin.clone.helper')
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

        $this->get('event_dispatcher')->dispatch(
            Events::REVERT,
            new RevertNodeAction(
                $node,
                $nodeTranslation,
                $newNodeVersion,
                $clonedPage,
                $nodeVersion,
                $page
            )
        );

        $this->get('session')->getFlashBag()->add('success', 'The page contents has been reverted');

        return $this->redirect(
            $this->generateUrl(
                'KunstmaanNodeBundle_nodes_edit',
                array(
                    'id'        => $id,
                    'subaction' => 'draft'
                )
            )
        );
    }

    /**
     * @Route(
     *      "/{id}/add",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanNodeBundle_nodes_add"
     * )
     * @Template()
     * @Method("POST")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     */
    public function addAction(Request $request, $id)
    {
        $this->init($request);
        /* @var Node $parentNode */
        $parentNode = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        // Check with Acl
        $this->checkPermission($parentNode, PermissionMap::PERMISSION_EDIT);

        $parentNodeTranslation = $parentNode->getNodeTranslation($this->locale, true);
        $parentNodeVersion     = $parentNodeTranslation->getPublicNodeVersion();
        $parentPage            = $parentNodeVersion->getRef($this->em);

        $type    = $this->validatePageType($request);
        $newPage = $this->createNewPage($request, $type);
        $newPage->setParent($parentPage);

        /* @var Node $nodeNewPage */
        $nodeNewPage     = $this->em->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($newPage, $this->locale, $this->user);
        $nodeTranslation = $nodeNewPage->getNodeTranslation(
            $this->locale,
            true
        );
        $weight          = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')
                ->getMaxChildrenWeight($parentNode, $this->locale) + 1;
        $nodeTranslation->setWeight($weight);

        if ($newPage->isStructureNode()) {
            $nodeTranslation->setSlug('');
        }

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->updateAcl($parentNode, $nodeNewPage);

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(
            Events::ADD_NODE,
            new NodeEvent(
                $nodeNewPage, $nodeTranslation, $nodeVersion, $newPage
            )
        );

        return $this->redirect(
            $this->generateUrl(
                'KunstmaanNodeBundle_nodes_edit',
                array('id' => $nodeNewPage->getId())
            )
        );
    }

    /**
     * @Route("/add-homepage", name="KunstmaanNodeBundle_nodes_add_homepage")
     * @Template()
     * @Method("POST")
     *
     * @return RedirectResponse
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     */
    public function addHomepageAction(Request $request)
    {
        $this->init($request);

        // Check with Acl
        if (false === $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }

        $type = $this->validatePageType($request);

        $newPage = $this->createNewPage($request, $type);

        /* @var Node $nodeNewPage */
        $nodeNewPage     = $this->em->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($newPage, $this->locale, $this->user);
        $nodeTranslation = $nodeNewPage->getNodeTranslation(
            $this->locale,
            true
        );
        $this->em->flush();

        // Set default permissions
        $this->get('kunstmaan_node.acl_permission_creator_service')
            ->createPermission($nodeNewPage);

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(
            Events::ADD_NODE,
            new NodeEvent(
                $nodeNewPage, $nodeTranslation, $nodeVersion, $newPage
            )
        );

        return $this->redirect(
            $this->generateUrl(
                'KunstmaanNodeBundle_nodes_edit',
                array('id' => $nodeNewPage->getId())
            )
        );
    }

    /**
     * @Route("/reorder", name="KunstmaanNodeBundle_nodes_reorder")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return string
     * @throws AccessDeniedException
     */
    public function reorderAction(Request $request)
    {
        $this->init($request);
        $nodes         = array();
        $nodeIds       = $request->get('nodes');
        $changeParents = $request->get('parent');

        foreach ($nodeIds as $id) {
            /* @var Node $node */
            $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
            $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);
            $nodes[] = $node;
        }

        $weight = 0;
        foreach ($nodes as $node) {

            $newParentId = isset($changeParents[$node->getId()]) ? $changeParents[$node->getId()] : null;
            if ($newParentId) {
                $parent = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($newParentId);
                $this->checkPermission($parent, PermissionMap::PERMISSION_EDIT);
                $node->setParent($parent);
                $this->em->persist($node);
                $this->em->flush($node);
            }


            /* @var NodeTranslation $nodeTranslation */
            $nodeTranslation = $node->getNodeTranslation($this->locale, true);

            if ($nodeTranslation) {
                $nodeVersion = $nodeTranslation->getPublicNodeVersion();
                $page        = $nodeVersion->getRef($this->em);

                $this->get('event_dispatcher')->dispatch(
                    Events::PRE_PERSIST,
                    new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
                );

                $nodeTranslation->setWeight($weight);
                $this->em->persist($nodeTranslation);
                $this->em->flush($nodeTranslation);

                $this->get('event_dispatcher')->dispatch(
                    Events::POST_PERSIST,
                    new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
                );

                $weight++;
            }
        }

        return new JsonResponse(
            array(
                'Success' => 'The node-translations for [' . $this->locale . '] have got new weight values'
            )
        );
    }

    /**
     * @Route(
     *      "/{id}/{subaction}",
     *      requirements={"id" = "\d+"},
     *      defaults={"subaction" = "public"},
     *      name="KunstmaanNodeBundle_nodes_edit"
     * )
     * @Template()
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param int     $id        The node id
     * @param string  $subaction The subaction (draft|public)
     *
     * @return RedirectResponse|array
     * @throws AccessDeniedException
     */
    public function editAction(Request $request, $id, $subaction)
    {
        $this->init($request);
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $tabPane = new TabPane(
            'todo',
            $request,
            $this->container->get('form.factory')
        );

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        if (!$nodeTranslation) {
            //try to find a parent node with the correct translation, if there is none allow copy.
            //if there is a parent but it doesn't have the language to copy to don't allow it
            $parentNode = $node->getParent();
            if ($parentNode) {
                $parentNodeTranslation = $parentNode->getNodeTranslation(
                    $this->locale,
                    true
                );
                $parentsAreOk          = false;

                if ($parentNodeTranslation) {
                    $parentsAreOk = $this->em->getRepository(
                        'KunstmaanNodeBundle:NodeTranslation'
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
                'KunstmaanNodeBundle:NodeAdmin:pagenottranslated.html.twig',
                array(
                    'node'                   => $node,
                    'nodeTranslations'       => $node->getNodeTranslations(
                        true
                    ),
                    'copyfromotherlanguages' => $parentsAreOk
                )
            );
        }

        $nodeVersion      = $nodeTranslation->getPublicNodeVersion();
        $draftNodeVersion = $nodeTranslation->getNodeVersion('draft');

        /* @var HasNodeInterface $page */
        $page        = null;
        $draft       = ($subaction == 'draft');
        $saveAsDraft = $request->get('saveasdraft');
        if ((!$draft && !empty($saveAsDraft)) || ($draft && is_null(
                    $draftNodeVersion
                ))
        ) {
            // Create a new draft version
            $draft            = true;
            $subaction        = "draft";
            $page             = $nodeVersion->getRef($this->em);
            $nodeVersion      = $this->createDraftVersion(
                $page,
                $nodeTranslation,
                $nodeVersion
            );
            $draftNodeVersion = $nodeVersion;
        } elseif ($draft) {
            $nodeVersion = $draftNodeVersion;
            $page        = $nodeVersion->getRef($this->em);
        } else {
            if ($request->getMethod() == 'POST') {
                //Check the version timeout and make a new nodeversion if the timeout is passed
                $thresholdDate = date(
                    "Y-m-d H:i:s",
                    time() - $this->container->getParameter(
                        "kunstmaan_node.version_timeout"
                    )
                );
                $updatedDate   = date(
                    "Y-m-d H:i:s",
                    strtotime($nodeVersion->getUpdated()->format("Y-m-d H:i:s"))
                );
                if ($thresholdDate >= $updatedDate) {
                    $page = $nodeVersion->getRef($this->em);
                    if ($nodeVersion == $nodeTranslation->getPublicNodeVersion()
                    ) {
                        $this->get('kunstmaan_node.admin_node.publisher')
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
        $pageAdminType    = $page->getDefaultAdminType();
        if (!is_object($pageAdminType) && is_string($pageAdminType)) {
            $pageAdminType = $this->container->get($pageAdminType);
        }
        $propertiesWidget->addType('main', $pageAdminType, $page);

        $nodeAdminType = $node->getDefaultAdminType();
        if (!is_object($nodeAdminType) && is_string($nodeAdminType)) {
            $nodeAdminType = $this->container->get($nodeAdminType);
        }
        $propertiesWidget->addType('node', $nodeAdminType, $node);
        $tabPane->addTab(new Tab('Properties', $propertiesWidget));

        // Menu tab
        $menuWidget = new FormWidget();
        $menuWidget->addType(
            'menunodetranslation',
            new NodeMenuTabTranslationAdminType($isStructureNode),
            $nodeTranslation
        );
        $menuWidget->addType('menunode', new NodeMenuTabAdminType($isStructureNode), $node);
        $tabPane->addTab(new Tab('Menu', $menuWidget));

        $this->get('event_dispatcher')->dispatch(
            Events::ADAPT_FORM,
            new AdaptFormEvent(
                $request,
                $tabPane,
                $page,
                $node,
                $nodeTranslation,
                $nodeVersion
            )
        );

        $tabPane->buildForm();

        if ($request->getMethod() == 'POST') {
            $tabPane->bindRequest($request);

            if ($tabPane->isValid()) {
                $this->get('event_dispatcher')->dispatch(
                    Events::PRE_PERSIST,
                    new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
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

                $this->get('event_dispatcher')->dispatch(
                    Events::POST_PERSIST,
                    new NodeEvent($node, $nodeTranslation, $nodeVersion, $page)
                );

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'The page has been edited'
                );

                $params = array(
                    'id'         => $node->getId(),
                    'subaction'  => $subaction,
                    'currenttab' => $tabPane->getActiveTab()
                );
                $params = array_merge(
                    $params,
                    $tabPane->getExtraParams($request)
                );

                return $this->redirect(
                    $this->generateUrl(
                        'KunstmaanNodeBundle_nodes_edit',
                        $params
                    )
                );
            }
        }

        $nodeVersions                = $this->em->getRepository(
            'KunstmaanNodeBundle:NodeVersion'
        )->findBy(
            array('nodeTranslation' => $nodeTranslation),
            array('updated' => 'ASC')
        );
        $queuedNodeTranslationAction = $this->em->getRepository(
            'KunstmaanNodeBundle:QueuedNodeTranslationAction'
        )->findOneBy(array('nodeTranslation' => $nodeTranslation));

        return array(
            'page'                        => $page,
            'entityname'                  => ClassLookup::getClass($page),
            'nodeVersions'                => $nodeVersions,
            'node'                        => $node,
            'nodeTranslation'             => $nodeTranslation,
            'draft'                       => $draft,
            'draftNodeVersion'            => $draftNodeVersion,
            'subaction'                   => $subaction,
            'tabPane'                     => $tabPane,
            'editmode'                    => true,
            'queuedNodeTranslationAction' => $queuedNodeTranslationAction
        );
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
            'KunstmaanNodeBundle:NodeVersion'
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

        $this->get('event_dispatcher')->dispatch(
            Events::CREATE_DRAFT_VERSION,
            new NodeEvent(
                $nodeTranslation->getNode(),
                $nodeTranslation,
                $nodeVersion,
                $page
            )
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
            $childNodePage    = $childNodeVersion->getRef($this->em);

            $this->get('event_dispatcher')->dispatch(
                Events::PRE_DELETE,
                new NodeEvent(
                    $childNode,
                    $childNodeTranslation,
                    $childNodeVersion,
                    $childNodePage
                )
            );

            $childNode->setDeleted(true);
            $this->em->persist($childNode);

            $children2 = $childNode->getChildren();
            $this->deleteNodeChildren($em, $user, $locale, $children2);

            $this->get('event_dispatcher')->dispatch(
                Events::POST_DELETE,
                new NodeEvent(
                    $childNode,
                    $childNodeTranslation,
                    $childNodeVersion,
                    $childNodePage
                )
            );
        }
    }

    /**
     * @param $originalNode
     * @param $nodeNewPage
     */
    private function updateAcl($originalNode, $nodeNewPage)
    {
        /* @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $this->container->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $strategy */
        $strategy         = $this->container->get(
            'security.acl.object_identity_retrieval_strategy'
        );
        $originalIdentity = $strategy->getObjectIdentity($originalNode);
        $originalAcl      = $aclProvider->findAcl($originalIdentity);

        $newIdentity = $strategy->getObjectIdentity($nodeNewPage);
        $newAcl      = $aclProvider->createAcl($newIdentity);

        $aces = $originalAcl->getObjectAces();
        /* @var EntryInterface $ace */
        foreach ($aces as $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                $newAcl->insertObjectAce($securityIdentity, $ace->getMask());
            }
        }
        $aclProvider->updateAcl($newAcl);
    }

    /**
     * @param Request $request
     * @param string  $type
     *
     * @return HasNodeInterface
     */
    private function createNewPage(Request $request, $type)
    {
        /* @var HasNodeInterface $newPage */
        $newPage = new $type();

        $title = $request->get('title');
        if (is_string($title) && !empty($title)) {
            $newPage->setTitle($title);
        } else {
            $newPage->setTitle('New page');
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
            throw new InvalidArgumentException(
                'Please specify a type of page you want to create'
            );
        }

        return $type;
    }
}
