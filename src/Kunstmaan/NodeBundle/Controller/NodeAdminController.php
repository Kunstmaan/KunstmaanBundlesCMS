<?php

namespace Kunstmaan\NodeBundle\Controller;

use DateTime;
use InvalidArgumentException;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\NodeBundle\AdminList\NodeAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeBundle\Event\RevertNodeAction;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Helper\Tabs\Tab;
use Kunstmaan\NodeBundle\Helper\Tabs\TabPane;
use Kunstmaan\NodeBundle\Repository\NodeVersionRepository;
use Kunstmaan\NodeBundle\Event\CopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * NodeAdminController
 */
class NodeAdminController extends Controller
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var string $locale
     */
    private $locale;

    /**
     * @var SecurityContextInterface $securityContext
     */
    private $securityContext;

    /**
     * @var User $user
     */
    private $user;

    /**
     * @var AclHelper $aclHelper
     */
    private $aclHelper;

    /**
     * init
     */
    private function init()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->locale = $this->getRequest()->getLocale();
        $this->securityContext = $this->container->get('security.context');
        $this->user = $this->securityContext->getToken()->getUser();
        $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
    }

    /**
     * @Route("/", name="KunstmaanNodeBundle_nodes")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $this->init();
        /* @var Node[] $topNodes */
        $topNodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->getTopNodes($this->locale, PermissionMap::PERMISSION_EDIT, $this->aclHelper, true);
        $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, null, PermissionMap::PERMISSION_EDIT, true, true);
        /* @var AdminList $adminlist */
        $adminlist = $this->get('kunstmaan_adminlist.factory')->createList(new NodeAdminListConfigurator($this->em, $this->aclHelper, $this->locale, PermissionMap::PERMISSION_EDIT));
        $adminlist->bindRequest($this->getRequest());

        return array(
            'topnodes' => $topNodes,
            'nodemenu' => $nodeMenu,
            'adminlist' => $adminlist,
        );
    }

    /**
     * @param int $id The node id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/copyfromotherlanguage", requirements={"_method" = "GET", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_copyfromotherlanguage")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function copyFromOtherLanguageAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $otherlanguage = $this->getRequest()->get('originallanguage');

        $otherLanguageNodeTranslation = $node->getNodeTranslation($otherlanguage, true);
        $otherLanguageNodeNodeVersion = $otherLanguageNodeTranslation->getPublicNodeVersion();
        $otherLanguagePage = $otherLanguageNodeNodeVersion->getRef($this->em);
        $myLanguagePage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($otherLanguagePage);
        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(Events::COPY_PAGE_TRANSLATION, new CopyPageTranslationNodeEvent($node, $nodeTranslation, $nodeVersion, $myLanguagePage, $otherLanguageNodeTranslation, $otherLanguageNodeNodeVersion, $otherLanguagePage, $otherlanguage));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $id)));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/createemptypage", requirements={"_method" = "GET", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_createemptypage")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function createEmptyPageAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $entityName = $node->getRefEntityName();
        /* @var HasNodeInterface $myLanguagePage */
        $myLanguagePage = new $entityName();
        $myLanguagePage->setTitle('New page');

        $this->em->persist($myLanguagePage);
        $this->em->flush(); // @todo move flush createNodeTranslation also flushes
        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(Events::ADD_EMPTY_PAGE_TRANSLATION, new NodeEvent($node, $nodeTranslation, $nodeVersion, $entityName));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $id)));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/publish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_publish")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function publishAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_PUBLISH);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->get('event_dispatcher')->dispatch(Events::PRE_PUBLISH, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeTranslation->setOnline(true);

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->get('event_dispatcher')->dispatch(Events::POST_PUBLISH, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $node->getId())));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/unpublish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_unpublish")
     * @Template()
     * @return RedirectResponse
     */
    public function unPublishAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_UNPUBLISH);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->get('event_dispatcher')->dispatch(Events::PRE_UNPUBLISH, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeTranslation->setOnline(false);

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->get('event_dispatcher')->dispatch(Events::POST_UNPUBLISH, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $node->getId())));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/delete", requirements={"_method" = "POST", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_delete")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_DELETE);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->get('event_dispatcher')->dispatch(Events::PRE_DELETE, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

        $nodeParent = $node->getParent();
        $node->setDeleted(true);
        $this->em->persist($node);

        $children = $node->getChildren();
        $this->deleteNodeChildren($this->em, $this->user, $this->locale, $children);
        $this->em->flush();

        $this->get('event_dispatcher')->dispatch(Events::POST_DELETE, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $nodeParent->getId())));
    }

    /**
     * @param int $id The node id
     *
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @Route("/{id}/revert", requirements={"_method" = "GET", "id" = "\d+"}, defaults={"subaction" = "public"}, name="KunstmaanNodeBundle_nodes_revert")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function revertAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $request = $this->getRequest();
        $version = $request->get('version');

        if (empty($version) || !is_numeric($version)) {
            throw new InvalidArgumentException('No version specified!');
        }

        /* @var NodeVersionRepository $nodeVersionRepo */
        $nodeVersionRepo = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion');
        /* @var NodeVersion $nodeVersion */
        $nodeVersion = $nodeVersionRepo->find($version);

        if (is_null($nodeVersion)) {
            throw new InvalidArgumentException('Version does not exist!');
        }

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $page = $nodeVersion->getRef($this->em);
        /* @var HasNodeInterface $clonedPage */
        $clonedPage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($page);
        $newNodeVersion = $nodeVersionRepo->createNodeVersionFor($clonedPage, $nodeTranslation, $this->user, $nodeVersion, 'draft');

        $nodeTranslation->setTitle($clonedPage->getTitle());
        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->get('event_dispatcher')->dispatch(Events::REVERT, new RevertNodeAction($node, $nodeTranslation, $newNodeVersion, $clonedPage, $nodeVersion, $page));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array(
            'id' => $id,
            'subaction' => 'draft'
        )));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @Route("/{id}/add", requirements={"_method" = "POST", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_add")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function addAction($id)
    {
        $this->init();
        /* @var Node $parentNode */
        $parentNode = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        // Check with Acl
        $this->checkPermission($parentNode, PermissionMap::PERMISSION_EDIT);

        $request = $this->getRequest();
        $parentNodeTranslation = $parentNode->getNodeTranslation($this->locale, true);
        $parentNodeVersion = $parentNodeTranslation->getPublicNodeVersion();
        $parentPage = $parentNodeVersion->getRef($this->em);
        $type = $request->get('type');

        if (empty($type)) {
            throw new InvalidArgumentException('Please specify a type of page to create!');
        }

        /* @var HasNodeInterface $newPage */
        $newPage = new $type();

        $title = $request->get('title');
        if (is_string($title) && !empty($title)) {
            $newPage->setTitle($title);
        } else {
            $newPage->setTitle('New page');
        }

        $this->em->persist($newPage);
        $this->em->flush(); // @todo move flush?

        $newPage->setParent($parentPage);

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository('KunstmaanNodeBundle:Node')->createNodeFor($newPage, $this->locale, $this->user);

        /* @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $this->container->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $strategy */
        $strategy = $this->container->get('security.acl.object_identity_retrieval_strategy');
        $parentIdentity = $strategy->getObjectIdentity($parentNode);
        $parentAcl = $aclProvider->findAcl($parentIdentity);

        $newIdentity = $strategy->getObjectIdentity($nodeNewPage);
        $newAcl = $aclProvider->createAcl($newIdentity);

        $aces = $parentAcl->getObjectAces();
        /* @var EntryInterface $ace */
        foreach ($aces as $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                $newAcl->insertObjectAce($securityIdentity, $ace->getMask());
            }
        }
        $aclProvider->updateAcl($newAcl);

        $nodeTranslation = $nodeNewPage->getNodeTranslation($this->locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(Events::ADD_NODE, new NodeEvent($nodeNewPage, $nodeTranslation, $nodeVersion, $newPage));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $nodeNewPage->getId())));
    }

    /**
     * @param int    $id        The node id
     * @param string $subaction The subaction (draft|public)
     *
     * @throws AccessDeniedException
     * @Route("/{id}/{subaction}", requirements={"_method" = "GET|POST", "id" = "\d+"}, defaults={"subaction" = "public"}, name="KunstmaanNodeBundle_nodes_edit")
     * @Template()
     *
     * @return RedirectResponse|array
     */
    public function editAction($id, $subaction)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $request = $this->getRequest();
        $tabPane = new TabPane('todo', $request, $this->container->get('form.factory')); // @todo initialize separate from constructor?

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        if (!$nodeTranslation) {
            $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, $node, PermissionMap::PERMISSION_EDIT, true, true);

            return $this->render('KunstmaanNodeBundle:NodeAdmin:pagenottranslated.html.twig', array('node' => $node, 'nodeTranslations' => $node->getNodeTranslations(true), 'nodemenu' => $nodeMenu));
        }

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $draftNodeVersion = $nodeTranslation->getNodeVersion('draft');

        /* @var HasNodeInterface $page */
        $page = null;
        $draft = ($subaction == 'draft');
        $saveAsDraft = $request->get('saveasdraft');
        if ((!$draft && !empty($saveAsDraft)) || ($draft && is_null($draftNodeVersion))) {
            // Create a new draft version
            $draft = true;
            $subaction = "draft";
            $page = $nodeVersion->getRef($this->em);
            $nodeVersion = $this->createDraftVersion($page, $nodeTranslation, $nodeVersion);
            $draftNodeVersion = $nodeVersion;
        } elseif ($draft) {
            $nodeVersion = $draftNodeVersion;
            $page = $nodeVersion->getRef($this->em);
        } else {
            $page = $nodeVersion->getRef($this->em);
        }

        $this->get('kunstmaan_node.actions_menu_builder')->setActiveNodeVersion($nodeVersion);

        // Building the form
        $propertiesTab = new Tab('Properties');
        $propertiesTab->addType('main', $page->getDefaultAdminType(), $page);
        $propertiesTab->addType('node', $node->getDefaultAdminType(), $node);
        $propertiesTab->addType('nodetranslation', $nodeTranslation->getDefaultAdminType(), $nodeTranslation);
        $tabPane->addTab($propertiesTab);

        $this->get('event_dispatcher')->dispatch(Events::ADAPT_FORM, new AdaptFormEvent($tabPane, $page, $node, $nodeTranslation, $nodeVersion));
        $tabPane->buildForm();

        if ($request->getMethod() == 'POST') {
            $tabPane->bindRequest($request);

            if ($tabPane->isValid()) {
                $this->get('event_dispatcher')->dispatch(Events::PRE_PERSIST, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

                $nodeTranslation->setTitle($page->getTitle());
                $this->em->persist($nodeTranslation);
                $tabPane->persist($this->em);
                $this->em->flush();

                $saveAndPublish = $request->get('saveandpublish');
                if (is_string($saveAndPublish) && !empty($saveAndPublish)) {
                    $subaction = 'public';
                    $nodeVersion = $this->createPublicVersion($page, $nodeTranslation, $nodeVersion);
                }

                $this->get('event_dispatcher')->dispatch(Events::POST_PERSIST, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

                return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array(
                    'id' => $node->getId(),
                    'subaction' => $subaction,
                    'currenttab' => $tabPane->getActiveTab(),
                )));
            }
        }

        $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, $node, PermissionMap::PERMISSION_EDIT, true, true);
        $topNodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->getTopNodes($this->locale, PermissionMap::PERMISSION_EDIT, $this->aclHelper);
        $nodeVersions = $nodeTranslation->getNodeVersions();

        return array(
            'topnodes' => $topNodes,
            'page' => $page,
            'entityname' => ClassLookup::getClass($page),
            'nodeVersions' => $nodeVersions,
            'nodemenu' => $nodeMenu,
            'node' => $node,
            'nodeTranslation' => $nodeTranslation,
            'draft' => $draft,
            'draftNodeVersion' => $draftNodeVersion,
            'subaction' => $subaction,
            'tabPane' => $tabPane
        );
    }

    /**
     * @param HasNodeInterface $page            The page
     * @param NodeTranslation  $nodeTranslation The node translation
     * @param NodeVersion      $nodeVersion     The node version
     *
     * @return mixed
     */
    private function createPublicVersion(HasNodeInterface $page, NodeTranslation $nodeTranslation, NodeVersion $nodeVersion)
    {
        $newPublicPage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($page);
        $nodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createNodeVersionFor($newPublicPage, $nodeTranslation, $this->user, $nodeVersion);
        $nodeTranslation->setPublicNodeVersion($nodeVersion);
        $nodeTranslation->setTitle($newPublicPage->getTitle());
        $nodeTranslation->setOnline(true);

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->get('event_dispatcher')->dispatch(Events::CREATE_PUBLIC_VERSION, new NodeEvent($nodeTranslation->getNode(), $nodeTranslation, $nodeVersion, $newPublicPage));

        return $nodeVersion;
    }

    /**
     * @param HasNodeInterface $page            The page
     * @param NodeTranslation  $nodeTranslation The node translation
     * @param NodeVersion      $nodeVersion     The node version
     *
     * @return NodeVersion
     */
    private function createDraftVersion(HasNodeInterface $page, NodeTranslation $nodeTranslation, NodeVersion $nodeVersion)
    {
        $publicPage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($page);
        /* @var NodeVersion $publicNodeVersion */
        $publicNodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createNodeVersionFor($publicPage, $nodeTranslation, $this->user, $nodeVersion->getOrigin(), 'public', $nodeVersion->getCreated());
        $nodeTranslation->setPublicNodeVersion($publicNodeVersion);
        $nodeVersion->setType('draft');
        $nodeVersion->setOrigin($publicNodeVersion);
        $nodeVersion->setCreated(new DateTime());

        $this->em->persist($nodeTranslation);
        $this->em->persist($nodeVersion);
        $this->em->flush();

        $this->get('event_dispatcher')->dispatch(Events::CREATE_DRAFT_VERSION, new NodeEvent($nodeTranslation->getNode(), $nodeTranslation, $nodeVersion, $page));

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
        if (false === $this->securityContext->isGranted($permission, $node)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param EntityManager   $em       The Entity Manager
     * @param User            $user     The user who deletes the children
     * @param string          $locale   The locale that was used
     * @param ArrayCollection $children The children array
     */
    private function deleteNodeChildren(EntityManager $em, User $user, $locale, ArrayCollection $children)
    {
        /* @var Node $childNode */
        foreach ($children as $childNode) {
            $childNodeTranslation = $childNode->getNodeTranslation($this->locale, true);
            $childNodeVersion = $childNodeTranslation->getPublicNodeVersion();
            $childNodePage = $childNodeVersion->getRef($this->em);

            $this->get('event_dispatcher')->dispatch(Events::PRE_DELETE, new NodeEvent($childNode, $childNodeTranslation, $childNodeVersion, $childNodePage));

            $childNode->setDeleted(true);
            $this->em->persist($childNode);

            $children2 = $childNode->getChildren();
            $this->deleteNodeChildren($em, $user, $locale, $children2);

            $this->get('event_dispatcher')->dispatch(Events::POST_DELETE, new NodeEvent($childNode, $childNodeTranslation, $childNodeVersion, $childNodePage));
        }
    }

}
