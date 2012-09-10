<?php

namespace Kunstmaan\AdminNodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminBundle\Entity\AddCommand;
use Kunstmaan\AdminBundle\Entity\EditCommand;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\AdminNodeBundle\AdminList\PageAdminListConfigurator;
use Kunstmaan\AdminNodeBundle\Form\SEOType;
use Kunstmaan\AdminNodeBundle\Helper\Event\Events;
use Kunstmaan\AdminNodeBundle\Helper\Event\PageEvent;
use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;

/**
 * PagesController
 */
class PagesController extends Controller
{
    /**
     * @Route("/", name="KunstmaanAdminNodeBundle_pages")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $securityContext = $this->container->get('security.context');
        $aclHelper = $this->container->get('kunstmaan.acl.helper');
        $topNodes = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, PermissionMap::PERMISSION_EDIT, $aclHelper, true);
        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, PermissionMap::PERMISSION_EDIT, true, true);
        $request    = $this->getRequest();
        $adminlist  = $this->get('adminlist.factory')->createList(new PageAdminListConfigurator($locale, PermissionMap::PERMISSION_EDIT), $em);
        $adminlist->setAclHelper($aclHelper);
        $adminlist->bindRequest($request);

        return array(
            'topnodes'  => $topNodes,
            'nodemenu' 	=> $nodeMenu,
            'adminlist' => $adminlist,
        );
    }

    /**
     * @param integer $id            The node id
     * @param string  $otherlanguage The locale from where the version must be copied
     *
     * @Route("/copyfromotherlanguage/{id}/{otherlanguage}", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_copyfromotherlanguage")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function copyFromOtherLanguageAction($id, $otherlanguage)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted(PermissionMap::PERMISSION_EDIT, $node)) {
            throw new AccessDeniedException();
        }

        $otherLanguageNodeTranslation = $node->getNodeTranslation($otherlanguage, true);
        $otherLanguagePage = $otherLanguageNodeTranslation->getPublicNodeVersion()->getRef($em);
        $myLanguagePage = $otherLanguagePage->deepClone($em);
        $node = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $locale, $node, $user);

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $id)));
    }

    /**
     * @param integer $id
     *
     * @Route("/{id}/createemptypage", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_createemptypage")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function createEmptyPageAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted(PermissionMap::PERMISSION_EDIT, $node)) {
            throw new AccessDeniedException();
        }

        $entityName = $node->getRefEntityname();
        $myLanguagePage = new $entityName();
        $myLanguagePage->setTitle('New page');

        $addCommand = new AddCommand($em, $user);
        $addCommand->execute('empty page added with locale: ' . $locale, array('entity' => $myLanguagePage));

        $node = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $locale, $node, $user);

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $id)));
    }

    /**
     * @param integer $id
     *
     * @Route("/{id}/publish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_edit_publish")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function publishAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $securityContext = $this->container->get('security.context');
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted(PermissionMap::PERMISSION_PUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        $nodeTranslation = $node->getNodeTranslation($locale, true);
        $nodeTranslation->setOnline(true);

        $user = $securityContext->getToken()->getUser();
        $editCommand = new EditCommand($em, $user);
        $editCommand->execute('published page "' . $nodeTranslation->getTitle() . '" for locale: ' . $locale, array('entity' => $nodeTranslation));

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $node->getId())));
    }

    /**
     * @param integer $id
     *
     * @Route("/{id}/unpublish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_edit_unpublish")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function unpublishAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $securityContext = $this->container->get('security.context');
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted(PermissionMap::PERMISSION_UNPUBLISH, $node)) {
            throw new AccessDeniedException();
        }

        $nodeTranslation = $node->getNodeTranslation($locale, true);
        $nodeTranslation->setOnline(false);

        $user = $securityContext->getToken()->getUser();
        $editCommand = new EditCommand($em, $user);
        $editCommand->execute('unpublished page "' . $nodeTranslation->getTitle() . '" for locale: ' . $locale, array('entity' => $nodeTranslation));

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $node->getId())));
    }

    /**
     * @param integer $id        The node id
     * @param string  $subaction The subaction (draft|public)
     *
     * @Route("/{id}/{subaction}", requirements={"_method" = "GET|POST", "id" = "\d+"}, defaults={"subaction" = "public"}, name="KunstmaanAdminNodeBundle_pages_edit")
     * @Template()
     *
     * @return RedirectResponse|array
     */
    public function editAction($id, $subaction)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $aclHelper = $this->container->get('kunstmaan.acl.helper');
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

        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted(PermissionMap::PERMISSION_EDIT, $node)) {
            throw new AccessDeniedException();
        }

        $draft = ($subaction == 'draft');
        $nodeTranslation = $node->getNodeTranslation($locale, true);
        if (!$nodeTranslation) {
            $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, $node, PermissionMap::PERMISSION_EDIT, true, true);

            return $this->render('KunstmaanAdminNodeBundle:Pages:pagenottranslated.html.twig', array('node' => $node, 'nodeTranslations' => $node->getNodeTranslations(true), 'nodemenu' => $nodeMenu));
        }

        $nodeVersions = $nodeTranslation->getNodeVersions();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $draftNodeVersion = $nodeTranslation->getNodeVersion('draft');

        $page = $em->getRepository($nodeVersion->getRefEntityname())->find($nodeVersion->getRefId());

        if ((!$draft && is_string($saveAsDraft) && $saveAsDraft != '') || ($draft && is_null($draftNodeVersion))) {
            $publicPage = $page->deepClone($em);
            $publicNodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($publicPage, $nodeTranslation, $user, 'public');
            $nodeTranslation->setPublicNodeVersion($publicNodeVersion);
            $nodeVersion->setType('draft');
            $em->persist($nodeTranslation);
            $em->persist($nodeVersion);
            $draft = true;
            $subaction = "draft";
        } elseif ($draft) {
            $nodeVersion = $draftNodeVersion;
            $page = $nodeVersion->getRef($em);
        }

        $this->get('admin_node.actions_menu_builder')->setActiveNodeVersion($nodeVersion);
        $addPage = $request->get("addpage");
        $addPageTitle = $request->get("addpagetitle");

        if (is_string($addPage) && $addPage != '') {
            $nodeNewPage = $this->addPage($em, $user, $locale, $page, $addPage, $addPageTitle);

            return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $nodeNewPage->getId(), 'currenttab' => $currentTab)));
        }

        $delete = $request->get('delete');
        if (is_string($delete) && $delete == 'true') {
            // Check with Acl
            if (false === $securityContext->isGranted(PermissionMap::PERMISSION_DELETE, $node)) {
                throw new AccessDeniedException();
            }

            //remove node and page
            $nodeParent = $node->getParent();
            $node->setDeleted(true);
            $updateCommand = new EditCommand($em, $user);
            $updateCommand->execute('deleted page "' . $page->getTitle() . '" with locale: ' . $locale, array('entity' => $node));
            $children = $node->getChildren();
            $this->deleteNodeChildren($em, $user, $locale, $children, $page);

            return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id'=>$nodeParent->getId(), 'currenttab' => $currentTab)));
        }

        $topNodes    = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, PermissionMap::PERMISSION_EDIT, $aclHelper);
        $formFactory = $this->container->get('form.factory');
        $formBuilder = $this->createFormBuilder();

        $seo = $nodeTranslation->getSEO();

        //add the specific data from the custom page
        $formBuilder->add('main', $page->getDefaultAdminType());
        $formBuilder->add('node', $node->getDefaultAdminType($this->container));
        $formBuilder->add('nodetranslation', $nodeTranslation->getDefaultAdminType($this->container));
        $formBuilder->add('seo', new SEOType());

        $bindingArray = array('node' => $node, 'main' => $page, 'nodetranslation'=> $nodeTranslation, 'seo' => $seo);
        if (method_exists($page, 'getExtraAdminTypes')) {
            foreach ($page->getExtraAdminTypes() as $key => $admintype) {
                $formBuilder->add($key, $admintype);
                $bindingArray[$key] = $page;
            }
        }
        $formBuilder->setData($bindingArray);

        //handle the pagepart functions (fetching, change form to reflect all fields, assigning data, etc...)
        $pagePartAdmins = array();
        if (method_exists($page, 'getPagePartAdminConfigurations')) {
            foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                $pagePartAdmin = $this->get('pagepartadmin.factory')->createList($pagePartAdminConfiguration, $em, $page, null, $this->container);
                $pagePartAdmin->preBindRequest($request);
                $pagePartAdmin->adaptForm($formBuilder, $formFactory);
                $pagePartAdmins[] = $pagePartAdmin;
            }
        }

        if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
            $permissionAdmin = $this->container->get('kunstmaan_admin.permissionadmin');
            // @todo Fetch permissionmap from page?
            $permissionMap = $this->container->get('security.acl.permission.map');
            $shellHelper = $this->container->get('kunstmaan.shell_helper');
            $permissionAdmin->initialize($node, $permissionMap, $shellHelper);
        }
        $form = $formBuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            foreach ($pagePartAdmins as $pagePartAdmin) {
                $pagePartAdmin->bindRequest($request);
            }
            if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
                $permissionAdmin->bindRequest($request);
            }
            if ($form->isValid()) {
                foreach ($pagePartAdmins as $pagePartAdmin) {
                    $pagePartAdmin->postBindRequest($request);
                }
                $nodeTranslation->setTitle($page->getTitle());
                $em->persist($node);
                $em->persist($nodeTranslation);

                $editCommand = new EditCommand($em, $user);
                $editCommand->execute('added pageparts to page "' . $page->getTitle() . '" with locale: ' . $locale, array('entity' => $page));

                if (is_string($saveAndPublish) && $saveAndPublish != '') {
                    $newPublicPage = $page->deepClone($em);
                    $nodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($newPublicPage, $nodeTranslation, $user, 'public');
                    $nodeTranslation->setPublicNodeVersion($nodeVersion);
                    $nodeTranslation->setTitle($newPublicPage->getTitle());
                    $nodeTranslation->setOnline(true);
                    $addCommand = new AddCommand($em, $user);
                    $addCommand->execute('saved and published page "' . $nodeTranslation->getTitle() . '" added with locale: ' . $locale, array('entity' => $nodeTranslation));
                    $draft = false;
                    $subaction = 'public';
                }

                $this->get('event_dispatcher')->dispatch(Events::POSTEDIT, new PageEvent($node, $nodeTranslation, $page));

                $redirectparams = array(
                    'id' => $node->getId(),
                    'subaction' => $subaction,
                    'currenttab' => $currentTab,
                    );
                if (isset($editPagePart)) {
                    $redirectparams['edit'] = $editPagePart;
                }

                return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', $redirectparams));
            }
        }

        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, $node, PermissionMap::PERMISSION_EDIT, true, true);

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
            'currenttab'	=> $currentTab,
        );
        if ($securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
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

        $aclProvider = $this->container->get('security.acl.provider');

        $parentIdentity = ObjectIdentity::fromDomainObject($nodeParent);
        $parentAcl = $aclProvider->findAcl($parentIdentity);

        $newIdentity = ObjectIdentity::fromDomainObject($nodeNewPage);
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
        $em = $this->getDoctrine()->getEntityManager();

        $parentId = $request->get('parentid');
        $parent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($parentId);

        $fromPosition = $request->get('fromposition');
        $afterPosition = $request->get('afterposition');

        foreach ($parent->getChildren() as $child) {
            if ($child->getSequencenumber() == $fromPosition) {
                if ($child->getSequencenumber() > $afterPosition) {
                    $child->setSequencenumber($afterPosition + 1);
                    $em->persist($child);
                } else {
                    $child->setSequencenumber($afterPosition);
                    $em->persist($child);
                }
            } else {
                if ($child->getSequencenumber() > $fromPosition && $child->getSequencenumber() <= $afterPosition) {
                    $newpos = $child->getSequencenumber()-1;
                    $child->setSequencenumber($newpos);
                    $em->persist($child);
                } else {
                    if ($child->getSequencenumber() < $fromPosition && $child->getSequencenumber() > $afterPosition) {
                        $newpos = $child->getSequencenumber()+1;
                        $child->setSequencenumber($newpos);
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
        $em = $this->getDoctrine()->getEntityManager();
        $request    = $this->getRequest();
        $locale     = $request->getSession()->getLocale();
        $aclHelper  = $this->container->get('kunstmaan.acl.helper');
        $securityContext = $this->container->get('security.context');
        $topnodes   = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, 'VIEW', $aclHelper);
        $nodeMenu   = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, 'VIEW', true, true);

        return array(
            'topnodes'    => $topnodes,
            'nodemenu'    => $nodeMenu,
        );
    }

}
