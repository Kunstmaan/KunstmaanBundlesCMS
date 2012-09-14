<?php

namespace Kunstmaan\AdminNodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Kunstmaan\AdminBundle\Entity\AddCommand;
use Kunstmaan\AdminBundle\Entity\EditCommand;
use Kunstmaan\AdminBundle\Helper\ClassLookup;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminNodeBundle\AdminList\PageAdminListConfigurator;
use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Form\SEOType;
use Kunstmaan\AdminNodeBundle\Helper\Event\Events;
use Kunstmaan\AdminNodeBundle\Helper\Event\PageEvent;
use Kunstmaan\AdminNodeBundle\Helper\NodeMenu;

/**
 * PagesController
 */
class PagesController extends Controller
{
    /* @var EntityManager $em */
    private $em;
    /* @var string $locale */
    private $locale;
    /* @var SecurityContextInterface $securityContext */
    private $securityContext;
    /* @var User $user */
    private $user;
    /* @var AclHelper $aclHelper */
    private $aclHelper;

    private function init()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->locale = $this->getRequest()->getLocale();
        $this->securityContext = $this->container->get('security.context');
        $this->user = $this->securityContext->getToken()->getUser();
        $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');

    }

    /**
     * @Route("/", name="KunstmaanAdminNodeBundle_pages")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $this->init();
        /* @var Node[] $topNodes */
        $topNodes = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($this->locale, PermissionMap::PERMISSION_EDIT, $this->aclHelper, true);
        $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, null, PermissionMap::PERMISSION_EDIT, true, true);
        /* @var AdminList $adminlist */
        $adminlist  = $this->get('kunstmaan_adminlist.factory')->createList(new PageAdminListConfigurator($this->locale, PermissionMap::PERMISSION_EDIT), $this->em);
        $adminlist->setAclHelper($this->aclHelper);
        $adminlist->bindRequest($this->getRequest());

        return array(
            'topnodes'  => $topNodes,
            'nodemenu' 	=> $nodeMenu,
            'adminlist' => $adminlist,
        );
    }

    /**
     * @param int    $id            The node id
     * @param string $otherlanguage The locale from where the version must be copied
     *
     * @Route("/copyfromotherlanguage/{id}/{otherlanguage}", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_copyfromotherlanguage")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function copyFromOtherLanguageAction($id, $otherlanguage)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_EDIT, $node)) {
            throw new AccessDeniedException();
        }

        $otherLanguageNodeTranslation = $node->getNodeTranslation($otherlanguage, true);
        $otherLanguagePage = $otherLanguageNodeTranslation->getPublicNodeVersion()->getRef($this->em);
        $myLanguagePage = $otherLanguagePage->deepClone($this->em);
        $this->em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $id)));
    }

    /**
     * @param int $id
     *
     * @Route("/{id}/createemptypage", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_createemptypage")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function createEmptyPageAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_EDIT, $node)) {
            throw new AccessDeniedException();
        }

        $entityName = $node->getRefEntityName();
        $myLanguagePage = new $entityName();
        $myLanguagePage->setTitle('New page');

        $addCommand = new AddCommand($this->em, $this->user);
        $addCommand->execute('empty page added with locale: ' . $this->locale, array('entity' => $myLanguagePage));

        $this->em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $id)));
    }

    /**
     * @param int $id
     *
     * @Route("/{id}/publish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_edit_publish")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function publishAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_PUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeTranslation->setOnline(true);

        $editCommand = new EditCommand($this->em, $this->user);
        $editCommand->execute('published page "' . $nodeTranslation->getTitle() . '" for locale: ' . $this->locale, array('entity' => $nodeTranslation));

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $node->getId())));
    }

    /**
     * @param int $id
     *
     * @Route("/{id}/unpublish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_edit_unpublish")
     * @Template()
     * @return RedirectResponse
     */
    public function unPublishAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_UNPUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $nodeTranslation->setOnline(false);

        $editCommand = new EditCommand($this->em, $this->user);
        $editCommand->execute('unpublished page "' . $nodeTranslation->getTitle() . '" for locale: ' . $this->locale, array('entity' => $nodeTranslation));

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $node->getId())));
    }

    /**
     * @param int    $id        The node id
     * @param string $subaction The subaction (draft|public)
     *
     * @Route("/{id}/{subaction}", requirements={"_method" = "GET|POST", "id" = "\d+"}, defaults={"subaction" = "public"}, name="KunstmaanAdminNodeBundle_pages_edit")
     * @Template()
     *
     * @return RedirectResponse|array
     */
    public function editAction($id, $subaction)
    {
        $this->init();
        $request = $this->getRequest();
        $saveAsDraft = $request->get('saveasdraft');
        $saveAndPublish = $request->get('saveandpublish');

        if ($request->request->get('currenttab')) {
            $currentTab = $request->request->get('currenttab');
        } elseif ($request->get('currenttab')) {
            $currentTab = $request->get('currenttab');
        } else {
            $currentTab = 'pageparts1';
        }

        if ($request->get('edit')) {
            $editPagePart = $request->get('edit');
        }

        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_EDIT, $node)) {
            throw new AccessDeniedException();
        }

        $draft = ($subaction == 'draft');
        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        if (!$nodeTranslation) {
            $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, $node, PermissionMap::PERMISSION_EDIT, true, true);

            return $this->render('KunstmaanAdminNodeBundle:Pages:pagenottranslated.html.twig', array('node' => $node, 'nodeTranslations' => $node->getNodeTranslations(true), 'nodemenu' => $nodeMenu));
        }

        $nodeVersions = $nodeTranslation->getNodeVersions();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $draftNodeVersion = $nodeTranslation->getNodeVersion('draft');

        $page = $this->em->getRepository($nodeVersion->getRefEntityName())->find($nodeVersion->getRefId());

        if ((!$draft && is_string($saveAsDraft) && $saveAsDraft != '') || ($draft && is_null($draftNodeVersion))) {
            $publicPage = $page->deepClone($this->em);
            $publicNodeVersion = $this->em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($publicPage, $nodeTranslation, $this->user, 'public');
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

        $this->get('kunstmaan_adminnode.actions_menu_builder')->setActiveNodeVersion($nodeVersion);
        $addPage = $request->get("addpage");
        $addPageTitle = $request->get("addpagetitle");

        if (is_string($addPage) && $addPage != '') {
            $nodeNewPage = $this->addPage($this->em, $this->user, $this->locale, $page, $addPage, $addPageTitle);

            return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $nodeNewPage->getId(), 'currenttab' => $currentTab)));
        }

        $delete = $request->get('delete');
        if (is_string($delete) && $delete == 'true') {
            // Check with Acl
            if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_DELETE, $node)) {
                throw new AccessDeniedException();
            }

            //remove node and page
            $nodeParent = $node->getParent();
            $node->setDeleted(true);
            $updateCommand = new EditCommand($this->em, $this->user);
            $updateCommand->execute('deleted page "' . $page->getTitle() . '" with locale: ' . $this->locale, array('entity' => $node));
            $children = $node->getChildren();
            $this->deleteNodeChildren($this->em, $this->user, $this->locale, $children, $page);

            return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id'=>$nodeParent->getId(), 'currenttab' => $currentTab)));
        }

        $formFactory = $this->container->get('form.factory');
        $formBuilder = $this->createFormBuilder();

        $seo = $nodeTranslation->getSEO();

        //add the specific data from the custom page
        $formBuilder->add('main', $page->getDefaultAdminType());
        $formBuilder->add('node', $node->getDefaultAdminType());
        $formBuilder->add('nodetranslation', $nodeTranslation->getDefaultAdminType());
        $formBuilder->add('seo', new SEOType());

        $bindingArray = array('node' => $node, 'main' => $page, 'nodetranslation'=> $nodeTranslation, 'seo' => $seo);
        if (method_exists($page, 'getExtraAdminTypes')) {
            foreach ($page->getExtraAdminTypes() as $key => $adminType) {
                $formBuilder->add($key, $adminType);
                $bindingArray[$key] = $page;
            }
        }
        $formBuilder->setData($bindingArray);

        //handle the pagepart functions (fetching, change form to reflect all fields, assigning data, etc...)
        $pagePartAdmins = array();
        if (method_exists($page, 'getPagePartAdminConfigurations')) {
            foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                $pagePartAdmin = $this->get('kunstmaan_pagepartadmin.factory')->createList($pagePartAdminConfiguration, $this->em, $page, null, $this->container);
                $pagePartAdmin->preBindRequest($request);
                $pagePartAdmin->adaptForm($formBuilder, $formFactory);
                $pagePartAdmins[] = $pagePartAdmin;
            }
        }

        if ($this->securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
            /* @var PermissionAdmin $permissionAdmin */
            $permissionAdmin = $this->container->get('kunstmaan_admin.permissionadmin');
            // @todo Fetch permissionmap from page?
            /* @var PermissionMap $permissionMap */
            $permissionMap = $this->container->get('security.acl.permission.map');
            $permissionAdmin->initialize($node, $permissionMap);
        }
        $form = $formBuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            foreach ($pagePartAdmins as $pagePartAdmin) {
                $pagePartAdmin->bindRequest($request);
            }
            if ($this->securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
                /* @var ShellHelper $shellHelper */
                $shellHelper = $this->container->get('kunstmaan_adminnode.shell_helper');
                $permissionAdmin->bindRequest($request, $shellHelper);
            }
            if ($form->isValid()) {
                foreach ($pagePartAdmins as $pagePartAdmin) {
                    $pagePartAdmin->postBindRequest($request);
                }
                $nodeTranslation->setTitle($page->getTitle());
                $this->em->persist($node);
                $this->em->persist($nodeTranslation);

                $editCommand = new EditCommand($this->em, $this->user);
                $editCommand->execute('added pageparts to page "' . $page->getTitle() . '" with locale: ' . $this->locale, array('entity' => $page));

                if (is_string($saveAndPublish) && $saveAndPublish != '') {
                    $newPublicPage = $page->deepClone($this->em);
                    $nodeVersion = $this->em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($newPublicPage, $nodeTranslation, $this->user, 'public');
                    $nodeTranslation->setPublicNodeVersion($nodeVersion);
                    $nodeTranslation->setTitle($newPublicPage->getTitle());
                    $nodeTranslation->setOnline(true);
                    $addCommand = new AddCommand($this->em, $this->user);
                    $addCommand->execute('saved and published page "' . $nodeTranslation->getTitle() . '" added with locale: ' . $this->locale, array('entity' => $nodeTranslation));
                    $subaction = 'public';
                }

                $this->get('event_dispatcher')->dispatch(Events::POSTEDIT, new PageEvent($node, $nodeTranslation, $page));

                $redirectParams = array(
                    'id' => $node->getId(),
                    'subaction' => $subaction,
                    'currenttab' => $currentTab,
                    );
                if (isset($editPagePart)) {
                    $redirectParams['edit'] = $editPagePart;
                }

                return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', $redirectParams));
            }
        }

        $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, $node, PermissionMap::PERMISSION_EDIT, true, true);
        $topNodes    = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($this->locale, PermissionMap::PERMISSION_EDIT, $this->aclHelper);

        $viewVariables = array(
            'topnodes'          => $topNodes,
            'page'              => $page,
            'entityname'        => ClassLookup::getClass($page),
            'form'              => $form->createView(),
            'pagepartadmins'    => $pagePartAdmins,
            'nodeVersions'      => $nodeVersions,
            'nodemenu'          => $nodeMenu,
            'node'              => $node,
            'nodeTranslation'   => $nodeTranslation,
            'draft'             => $draft,
            'draftNodeVersion'  => $draftNodeVersion,
            'subaction'         => $subaction,
            'currenttab'	    => $currentTab,
        );
        if ($this->securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
            $viewVariables['permissionadmin'] = $permissionAdmin;
        }

        return $viewVariables;
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
    protected function addPage($em, $user, $locale, $parentPage, $pageType, $pageTitle = '')
    {
        $newPage = new $pageType();

        if (is_string($pageTitle) && $pageTitle != '') {
            $newPage->setTitle($pageTitle);
        } else {
            $newPage->setTitle('New page');
        }

        $addCommand = new AddCommand($em, $user);
        $addCommand->execute('page "' . $newPage->getTitle() .'" added with locale: ' . $locale, array('entity'=> $newPage));

        $nodeParent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($parentPage);
        $newPage->setParent($parentPage);

        $nodeNewPage = $em->getRepository('KunstmaanAdminNodeBundle:Node')->createNodeFor($newPage, $locale, $user);
        $em->persist($nodeNewPage);
        $em->flush();

        /* @var AclProviderInterface $aclProvider */
        $aclProvider = $this->container->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $strategy */
        $strategy = $this->container->get('security.acl.object_identity_retrieval_strategy');
        $parentIdentity = $strategy->getObjectIdentity($nodeParent);
        $parentAcl = $aclProvider->findAcl($parentIdentity);

        $newIdentity = $strategy->getObjectIdentity($nodeNewPage);
        $newAcl = $aclProvider->createAcl($newIdentity);

        $aces = $parentAcl->getObjectAces();
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
     * @param Node[]           $children The children array
     * @param HasNodeInterface $page     The node
     */
    private function deleteNodeChildren($em, $user, $locale, $children, $page)
    {
        foreach ($children as $child) {
            $child->setDeleted(true);
            $updateCommand = new EditCommand($em, $user);
            $updateCommand->execute('deleted child for page "' . $page->getTitle() . '" with locale: ' . $locale, array('entity'=> $child));
            $children2 = $child->getChildren();
            $this->deleteNodeChildren($em, $user, $locale, $children2, $page);
        }
    }

    /**
     * @Route("/movenodes", name="KunstmaanAdminNodeBundle_pages_movenodes")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function moveNodesAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $parentId = $request->get('parentid');
        $parent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($parentId);

        $fromPosition = $request->get('fromposition');
        $afterPosition = $request->get('afterposition');

        foreach ($parent->getChildren() as $child) {
            if ($child->getSequenceNumber() == $fromPosition) {
                if ($child->getSequenceNumber() > $afterPosition) {
                    $child->setSequenceNumber($afterPosition + 1);
                    $em->persist($child);
                } else {
                    $child->setSequenceNumber($afterPosition);
                    $em->persist($child);
                }
            } else {
                if ($child->getSequenceNumber() > $fromPosition && $child->getSequenceNumber() <= $afterPosition) {
                    $newPos = $child->getSequenceNumber()-1;
                    $child->setSequenceNumber($newPos);
                    $em->persist($child);
                } else {
                    if ($child->getSequenceNumber() < $fromPosition && $child->getSequenceNumber() > $afterPosition) {
                        $newPos = $child->getSequenceNumber()+1;
                        $child->setSequenceNumber($newPos);
                        $em->persist($child);
                    }
                }
            }
            $em->flush();
        }

        return array('success' => true);
    }

    /**
     * @Route("/ckselecturl", name="KunstmaanAdminNodeBundle_ckselecturl")
     * @Template()
     *
     * @return array
     */
    public function ckSelectLinkAction()
    {
        $this->init();
        $topNodes   = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($this->locale, PermissionMap::PERMISSION_VIEW, $this->aclHelper);
        $nodeMenu   = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $this->locale, null, PermissionMap::PERMISSION_VIEW, true, true);

        return array(
            'topnodes'    => $topNodes,
            'nodemenu'    => $nodeMenu,
        );
    }
}
