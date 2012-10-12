<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
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

use Kunstmaan\AdminBundle\Entity\AddCommand;
use Kunstmaan\AdminBundle\Entity\EditCommand;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminBundle\Entity\DeepCloneableInterface;
use Kunstmaan\NodeBundle\AdminList\PageAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Form\SEOType;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\PageEvent;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Tabs\Tab;
use Kunstmaan\NodeBundle\Tabs\TabPane;
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

        $this->checkPermission(PermissionMap::PERMISSION_EDIT, $node);

        $otherLanguageNodeTranslation = $node->getNodeTranslation($otherlanguage, true);
        $otherLanguagePage = $otherLanguageNodeTranslation->getPublicNodeVersion()->getRef($this->em);
        /* @var DeepCloneableInterface $otherLanguagePage */
        $myLanguagePage = $otherLanguagePage->deepClone($this->em);
        $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);

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

        $this->checkPermission(PermissionMap::PERMISSION_EDIT, $node);

        $entityName = $node->getRefEntityName();
        /* @var HasNodeInterface $myLanguagePage */
        $myLanguagePage = new $entityName();
        $myLanguagePage->setTitle('New page');

        $addCommand = new AddCommand($this->em, $this->user);  // @todo: remove commands
        $addCommand->execute('empty page added with locale: ' . $this->locale, array('entity' => $myLanguagePage));

        $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);

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

        $this->checkPermission(PermissionMap::PERMISSION_PUBLISH, $node);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeTranslation->setOnline(true);

        $editCommand = new EditCommand($this->em, $this->user);  // @todo: remove commands
        $editCommand->execute('published page "' . $nodeTranslation->getTitle() . '" for locale: ' . $this->locale, array('entity' => $nodeTranslation));

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

        // Check with Acl
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_UNPUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeTranslation->setOnline(false);

        $editCommand = new EditCommand($this->em, $this->user);  // @todo: remove commands
        $editCommand->execute('unpublished page "' . $nodeTranslation->getTitle() . '" for locale: ' . $this->locale, array('entity' => $nodeTranslation));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $node->getId())));
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
        $page = $this->em->getRepository($nodeVersion->getRefEntityName())->find($nodeVersion->getRefId());

        $draft = ($subaction == 'draft');
        $saveAsDraft = $request->get('saveasdraft');
        if ((!$draft && is_string($saveAsDraft) && $saveAsDraft != '') || ($draft && is_null($draftNodeVersion))) {
            /* @var DeepCloneableInterface $page */
            $publicPage = $page->deepClone($this->em);
            $publicNodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createNodeVersionFor($publicPage, $nodeTranslation, $this->user, 'public');
            $nodeTranslation->setPublicNodeVersion($publicNodeVersion);
            $nodeVersion->setType('draft');
            $this->em->persist($nodeTranslation);
            $this->em->persist($nodeVersion);
            $draft = true;
            $subaction = "draft";
        } elseif ($draft) {
            $nodeVersion = $draftNodeVersion;
            $page = $nodeVersion->getRef($this->em);
        }

        $this->get('kunstmaan_node.actions_menu_builder')->setActiveNodeVersion($nodeVersion);
        $addPage = $request->get("addpage");

        if (is_string($addPage) && $addPage != '') {
            $nodeNewPage = $this->addPage($this->em, $this->user, $this->locale, $page, $addPage, $request->get("addpagetitle"));

            return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $nodeNewPage->getId(), 'currenttab' => $tabPane->getActiveTab())));
        }

        $delete = $request->get('delete');
        if (is_string($delete) && $delete == 'true') {
            // Check with Acl
            $this->checkPermission(PermissionMap::PERMISSION_DELETE, $node);

            //remove node and page
            $nodeParent = $node->getParent();
            $node->setDeleted(true);
            $updateCommand = new EditCommand($this->em, $this->user);  // @todo: remove commands
            $updateCommand->execute('deleted page "' . $page->getTitle() . '" with locale: ' . $this->locale, array('entity' => $node));
            $children = $node->getChildren();
            $this->deleteNodeChildren($this->em, $this->user, $this->locale, $children, $page);

            return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', array('id' => $nodeParent->getId(), 'currenttab' => $tabPane->getActiveTab())));
        }

        // Building the form
        $tabPane->addTab(new Tab('Properties', array(
            'main' => $page->getDefaultAdminType(),
            'node' => $node->getDefaultAdminType(),
            'nodetranslation' => $nodeTranslation->getDefaultAdminType()
        ), array(
            'main' => $page,
            'node' => $node,
            'nodetranslation' => $nodeTranslation
        )));

        $tabPane->addTab(new Tab('Seo', array(
            'seo' => new SEOType()
        ), array(
            'seo' => $nodeTranslation->getSEO()
        )));

        $this->get('event_dispatcher')->dispatch(Events::ADAPT_FORM, new AdaptFormEvent($tabPane, $page, $node, $nodeTranslation, $nodeVersion));
        $tabPane->buildForm($request);

        if ($request->getMethod() == 'POST') {
            $tabPane->bindRequest($request);

            if ($tabPane->isValid()) {
                $nodeTranslation->setTitle($page->getTitle());
                $this->em->persist($node);
                $this->em->persist($nodeTranslation);

                $tabPane->persist($this->em, $request);

                $editCommand = new EditCommand($this->em, $this->user); // @todo: remove commands
                $editCommand->execute('added pageparts to page "' . $page->getTitle() . '" with locale: ' . $this->locale, array('entity' => $page));

                $saveAndPublish = $request->get('saveandpublish');
                if (is_string($saveAndPublish) && $saveAndPublish != '') {
                    /* @var HasNodeInterface $newPublicPage */
                    $newPublicPage = $page->deepClone($this->em);
                    $nodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createNodeVersionFor($newPublicPage, $nodeTranslation, $this->user, 'public');
                    $nodeTranslation->setPublicNodeVersion($nodeVersion);
                    $nodeTranslation->setTitle($newPublicPage->getTitle());
                    $nodeTranslation->setOnline(true);
                    $addCommand = new AddCommand($this->em, $this->user); // @todo: remove commands
                    $addCommand->execute('saved and published page "' . $nodeTranslation->getTitle() . '" added with locale: ' . $this->locale, array('entity' => $nodeTranslation));
                    $subaction = 'public';
                }

                $this->get('event_dispatcher')->dispatch(Events::POSTEDIT, new PageEvent($node, $nodeTranslation, $page));

                $redirectParams = array(
                    'id' => $node->getId(),
                    'subaction' => $subaction,
                    'currenttab' => $tabPane->getActiveTab(),
                );

                if ($editPagePart = $request->get('edit') && isset($editPagePart)) {
                    $redirectParams['edit'] = $editPagePart;
                }

                return $this->redirect($this->generateUrl('KunstmaanNodeBundle_pages_edit', $redirectParams));
            }
        }

        $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, $node, PermissionMap::PERMISSION_EDIT, true, true);
        $topNodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->getTopNodes($this->locale, PermissionMap::PERMISSION_EDIT, $this->aclHelper);
        $nodeVersions = $nodeTranslation->getNodeVersions();

        $viewVariables = array(
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

        if (isset($permissionAdmin) && $this->securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
            $viewVariables['permissionadmin'] = $permissionAdmin;
        }

        return $viewVariables;
    }

    private function checkPermission(Node $node, $permission)
    {
        if (false === $this->securityContext->isGranted($permission, $node)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param EntityManager    $em         The Entity Manager
     * @param User             $user       The user who adds the page
     * @param string           $locale     The locale
     * @param HasNodeInterface $parentPage The page will be added under this parent page
     * @param string           $pageType   The class name
     * @param string           $pageTitle  The new page title
     *
     * @return Node
     */
    protected function addPage(EntityManager $em, User $user, $locale, HasNodeInterface $parentPage, $pageType, $pageTitle = '')
    {
        /* @var HasNodeInterface $newPage */
        $newPage = new $pageType();

        if (is_string($pageTitle) && $pageTitle != '') {
            $newPage->setTitle($pageTitle);
        } else {
            $newPage->setTitle('New page');
        }

        $addCommand = new AddCommand($em, $user);  // @todo: remove commands
        $addCommand->execute('page "' . $newPage->getTitle() . '" added with locale: ' . $locale, array('entity' => $newPage));

        $nodeParent = $em->getRepository('KunstmaanNodeBundle:Node')->getNodeFor($parentPage);
        $newPage->setParent($parentPage);

        $nodeNewPage = $em->getRepository('KunstmaanNodeBundle:Node')->createNodeFor($newPage, $locale, $user);
        $em->persist($nodeNewPage);
        $em->flush();

        /* @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $this->container->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $strategy */
        $strategy = $this->container->get('security.acl.object_identity_retrieval_strategy');
        $parentIdentity = $strategy->getObjectIdentity($nodeParent);
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

        return $nodeNewPage;
    }

    /**
     * @param EntityManager    $em       The Entity Manager
     * @param User             $user     The user who deletes the children
     * @param string           $locale   The locale that was used
     * @param ArrayCollection  $children The children array
     * @param HasNodeInterface $page     The node
     */
    private function deleteNodeChildren(EntityManager $em, User $user, $locale, ArrayCollection $children, HasNodeInterface $page)
    {
        /* @var Node $child */
        foreach ($children as $child) {
            $child->setDeleted(true);
            $updateCommand = new EditCommand($em, $user);  // @todo: remove commands
            $updateCommand->execute('deleted child for page "' . $page->getTitle() . '" with locale: ' . $locale, array('entity' => $child));
            $children2 = $child->getChildren();
            $this->deleteNodeChildren($em, $user, $locale, $children2, $page);
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
