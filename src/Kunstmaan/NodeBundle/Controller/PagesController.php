<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Acl\Model\EntryInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\AdminList\PageAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Form\SEOType;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\PageEvent;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Helper\Tabs\Tab;
use Kunstmaan\NodeBundle\Helper\Tabs\TabPane;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * PagesController
 */
class PagesController extends Controller
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
     * @Route("/", name="KunstmaanNodeBundle_pages")
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
        $adminlist = $this->get('kunstmaan_adminlist.factory')->createList(new PageAdminListConfigurator($this->em, $this->aclHelper, $this->locale, PermissionMap::PERMISSION_EDIT));
        $adminlist->bindRequest($this->getRequest());

        return array(
            'topnodes' => $topNodes,
            'nodemenu' => $nodeMenu,
            'adminlist' => $adminlist,
        );
    }

    /**
     * @param int    $id            The node id
     * @param string $otherlanguage The locale from where the version must be copied
     *
     * @throws AccessDeniedException
     * @Route("/copyfromotherlanguage/{id}/{otherlanguage}", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_pages_copyfromotherlanguage")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function copyFromOtherLanguageAction($id, $otherlanguage)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $otherLanguageNodeTranslation = $node->getNodeTranslation($otherlanguage, true);
        $otherLanguageNodeNodeVersion = $otherLanguageNodeTranslation->getPublicNodeVersion();
        $otherLanguagePage = $otherLanguageNodeNodeVersion->getRef($this->em);
        $myLanguagePage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($otherLanguagePage);
        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        // @todo log using events
        $this->get('event_dispatcher')->dispatch(Events::COPY_PAGE_TRANSLATION, new PageEvent($node, $nodeTranslation, $nodeVersion, $myLanguagePage));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $id)));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/createemptypage", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_pages_createemptypage")
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

        // @todo log using events
        $this->get('event_dispatcher')->dispatch(Events::ADD_EMPTY_PAGE_TRANSLATION, new PageEvent($node, $nodeTranslation, $nodeVersion, $entityName));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $id)));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/publish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_pages_edit_publish")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function publishAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->checkPermission($node, PermissionMap::PERMISSION_PUBLISH);

        $this->get('event_dispatcher')->dispatch(Events::PRE_PUBLISH, new PageEvent($node, $nodeTranslation, $nodeVersion, $page));

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeTranslation->setOnline(true);

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        // @todo log using events
        $this->get('event_dispatcher')->dispatch(Events::POST_PUBLISH, new PageEvent($node, $nodeTranslation, $nodeVersion, $page));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $node->getId())));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/unpublish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_pages_edit_unpublish")
     * @Template()
     * @return RedirectResponse
     */
    public function unPublishAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->checkPermission($node, PermissionMap::PERMISSION_UNPUBLISH);

        $this->get('event_dispatcher')->dispatch(Events::PRE_UNPUBLISH, new PageEvent($node, $nodeTranslation, $nodeVersion, $page));

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeTranslation->setOnline(false);

        $this->em->persist($nodeTranslation);
        $this->em->flush();

        // @todo log using events
        $this->get('event_dispatcher')->dispatch(Events::POST_UNPUBLISH, new PageEvent($node, $nodeTranslation, $nodeVersion, $page));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $node->getId())));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/delete", requirements={"_method" = "POST", "id" = "\d+"}, name="KunstmaanNodeBundle_pages_delete")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $page = $nodeVersion->getRef($this->em);

        $this->checkPermission($node, PermissionMap::PERMISSION_DELETE);

        $this->get('event_dispatcher')->dispatch(Events::PRE_DELETE, new PageEvent($node, $nodeTranslation, $nodeVersion, $page));

        $nodeParent = $node->getParent();
        $node->setDeleted(true);
        $this->em->persist($node);

        // @todo log using events

        $children = $node->getChildren();
        $this->deleteNodeChildren($this->em, $this->user, $this->locale, $children);
        $this->em->flush();

        $this->get('event_dispatcher')->dispatch(Events::POST_DELETE, new PageEvent($node, $nodeTranslation, $nodeVersion, $page));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $nodeParent->getId())));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/add", requirements={"_method" = "POST", "id" = "\d+"}, name="KunstmaanNodeBundle_pages_add")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function addAction($id)
    {
        $this->init();
        $request = $this->getRequest();
        /* @var Node $parentNode */
        $parentNode = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        $parentNodeTranslation = $parentNode->getNodeTranslation($this->locale, true);
        $parentNodeVersion = $parentNodeTranslation->getPublicNodeVersion();
        $parentPage = $parentNodeVersion->getRef($this->em);

        // Check with Acl
        $this->checkPermission($parentNode, PermissionMap::PERMISSION_EDIT);

        $type = $request->get('type'); // @todo .. what if no type has been given?
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

        // @todo log using events

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

        $this->get('event_dispatcher')->dispatch(Events::ADD_NODE, new PageEvent($nodeNewPage, $nodeTranslation, $nodeVersion, $newPage));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $nodeNewPage->getId())));
    }

    /**
     * @param int    $id        The node id
     * @param string $subaction The subaction (draft|public)
     *
     * @throws AccessDeniedException
     * @Route("/{id}/{subaction}", requirements={"_method" = "GET|POST", "id" = "\d+"}, defaults={"subaction" = "public"}, name="KunstmaanNodeBundle_pages_edit")
     * @Template()
     *
     * @return RedirectResponse|array
     */
    public function editAction($id, $subaction)
    {

        $this->init();
        $request = $this->getRequest();

        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);

        $tabPane = new TabPane('todo', $request, $this->container->get('form.factory')); // @todo initialize separate from constructor?

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        if (!$nodeTranslation) {
            $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, $node, PermissionMap::PERMISSION_EDIT, true, true);

            return $this->render('KunstmaanNodeBundle:Pages:pagenottranslated.html.twig', array('node' => $node, 'nodeTranslations' => $node->getNodeTranslations(true), 'nodemenu' => $nodeMenu));
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

        $seoTab = new Tab('SEO');
        $seoTab->addType('seo', new SEOType(), $nodeTranslation->getSEO());
        $tabPane->addTab($seoTab);

        $this->get('event_dispatcher')->dispatch(Events::ADAPT_FORM, new AdaptFormEvent($tabPane, $page, $node, $nodeTranslation, $nodeVersion));
        $tabPane->buildForm();

        if ($request->getMethod() == 'POST') {
            $tabPane->bindRequest($request);

            if ($tabPane->isValid()) {
                $this->get('event_dispatcher')->dispatch(Events::PRE_PERSIST, new PageEvent($node, $nodeTranslation, $nodeVersion, $page));

                $nodeTranslation->setTitle($page->getTitle());
                $this->em->persist($nodeTranslation);
                $tabPane->persist($this->em);
                $this->em->flush();

                // @todo log using events

                $saveAndPublish = $request->get('saveandpublish');
                if (is_string($saveAndPublish) && !empty($saveAndPublish)) {
                    $subaction = 'public';
                    $nodeVersion = $this->createPublicVersion($page, $nodeTranslation);
                }

                $this->get('event_dispatcher')->dispatch(Events::POST_PERSIST, new PageEvent($node, $nodeTranslation, $nodeVersion, $page));

                return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array(
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
     *
     * @return mixed
     */
    public function createPublicVersion(HasNodeInterface $page, NodeTranslation $nodeTranslation)
    {
        $newPublicPage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($page);
        $nodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createNodeVersionFor($newPublicPage, $nodeTranslation, $this->user, 'public');
        $nodeTranslation->setPublicNodeVersion($nodeVersion);
        $nodeTranslation->setTitle($newPublicPage->getTitle());
        $nodeTranslation->setOnline(true);
        $this->em->persist($nodeTranslation);
        $this->em->flush();

        // @todo log using events
        $this->get('event_dispatcher')->dispatch(Events::CREATE_PUBLIC_VERSION, new PageEvent($nodeTranslation->getNode(), $nodeTranslation, $nodeVersion, $newPublicPage));

        return $nodeVersion;
    }

    /**
     * @param HasNodeInterface  $page            The page
     * @param NodeTranslation   $nodeTranslation The node translation
     * @param NodeVersion       $nodeVersion     The node version
     *
     * @return NodeVersion
     */
    private function createDraftVersion(HasNodeInterface $page, NodeTranslation $nodeTranslation, NodeVersion $nodeVersion)
    {
        $publicPage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($page);
        $publicNodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createNodeVersionFor($publicPage, $nodeTranslation, $this->user, 'public');
        $nodeTranslation->setPublicNodeVersion($publicNodeVersion);
        $nodeVersion->setType('draft');
        $this->em->persist($nodeTranslation);
        $this->em->persist($nodeVersion);
        $this->em->flush();

        // @todo log using events
        $this->get('event_dispatcher')->dispatch(Events::CREATE_DRAFT_VERSION, new PageEvent($nodeTranslation->getNode(), $nodeTranslation, $nodeVersion, $page));

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
     * @param EntityManager    $em       The Entity Manager
     * @param User             $user     The user who deletes the children
     * @param string           $locale   The locale that was used
     * @param ArrayCollection  $children The children array
     */
    private function deleteNodeChildren(EntityManager $em, User $user, $locale, ArrayCollection $children)
    {
        /* @var Node $childNode */
        foreach ($children as $childNode) {
            $childNodeTranslation = $childNode->getNodeTranslation($this->locale, true);
            $childNodeVersion = $childNodeTranslation->getPublicNodeVersion();
            $childNodePage = $childNodeVersion->getRef($this->em);

            $this->get('event_dispatcher')->dispatch(Events::PRE_DELETE, new PageEvent($childNode, $childNodeTranslation, $childNodeVersion, $childNodePage));

            $childNode->setDeleted(true);
            $this->em->persist($childNode);

            // @todo log using events

            $children2 = $childNode->getChildren();
            $this->deleteNodeChildren($em, $user, $locale, $children2);

            $this->get('event_dispatcher')->dispatch(Events::POST_DELETE, new PageEvent($childNode, $childNodeTranslation, $childNodeVersion, $childNodePage));
        }
    }

    /**
     * @Route("/ckselecturl", name="KunstmaanNodeBundle_ckselecturl")
     * @Template()
     *
     * @return array
     * @todo move to separate controller?
     */
    public function ckSelectLinkAction()
    {
        $this->init();
        $topNodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->getTopNodes($this->locale, PermissionMap::PERMISSION_VIEW, $this->aclHelper);
        $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, null, PermissionMap::PERMISSION_VIEW, true, true);

        return array(
            'topnodes' => $topNodes,
            'nodemenu' => $nodeMenu,
        );
    }

    /**
     * Select a link
     *
     * @Route   ("/pageparts/selecturl", name="KunstmaanNodeBundle_selecturl")
     * @Template()
     *
     * @return array
     * @todo move to separate controller?
     */
    public function selectLinkAction()
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        /* @var AclHelper $aclHelper */
        $aclHelper = $this->container->get('kunstmaan.acl.helper');
        $topNodes = $em->getRepository('KunstmaanNodeBundle:Node')->getTopNodes($locale, PermissionMap::PERMISSION_VIEW, $aclHelper, true);
        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, PermissionMap::PERMISSION_VIEW, false, true);

        return array('topnodes' => $topNodes, 'nodemenu' => $nodeMenu);
    }
}
