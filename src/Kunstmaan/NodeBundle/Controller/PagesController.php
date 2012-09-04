<?php

namespace Kunstmaan\AdminNodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Kunstmaan\AdminBundle\Entity\AddCommand;
use Kunstmaan\AdminBundle\Entity\EditCommand;
use Kunstmaan\AdminBundle\Helper\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\ClassLookup;
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
    /**
     * @Route("/", name="KunstmaanAdminNodeBundle_pages")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        /* @var AclHelper $aclHelper */
        $aclHelper = $this->container->get('kunstmaan.acl.helper');
        /* @var Node[] $topNodes */
        $topNodes = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, 'EDIT', $aclHelper, true);
        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, 'EDIT', true, true);
        /* @var AdminList $adminlist */
        $adminlist  = $this->get('adminlist.factory')->createList(new PageAdminListConfigurator($user, 'EDIT', $locale), $em);
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
        $em = $this->getDoctrine()->getManager();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        /* @var Node $node */
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted('EDIT', $node)) {
            throw new AccessDeniedException();
        }

        $otherLanguageNodeTranslation = $node->getNodeTranslation($otherlanguage, true);
        $otherLanguagePage = $otherLanguageNodeTranslation->getPublicNodeVersion()->getRef($em);
        $myLanguagePage = $otherLanguagePage->deepClone($em);
        $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $locale, $node, $user);

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
        $em = $this->getDoctrine()->getManager();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        /* @var Node $node */
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted('EDIT', $node)) {
            throw new AccessDeniedException();
        }

        $entityName = $node->getRefEntityname();
        $myLanguagePage = new $entityName();
        $myLanguagePage->setTitle('New page');

        $addCommand = new AddCommand($em, $user);
        $addCommand->execute('empty page added with locale: ' . $locale, array('entity' => $myLanguagePage));

        $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $locale, $node, $user);

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
        $em = $this->getDoctrine()->getManager();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        /* @var Node $node */
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted('PUBLISH', $node)) {
            throw new AccessDeniedException();
        }

        $nodeTranslation = $node->getNodeTranslation($locale, true);
        $nodeTranslation->setOnline(true);

        $editCommand = new EditCommand($em, $user);
        $editCommand->execute('published page "' . $nodeTranslation->getTitle() . '" for locale: ' . $locale, array('entity' => $nodeTranslation));

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $node->getId())));
    }

    /**
     * @param integer $id
     *
     * @Route("/{id}/unpublish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminNodeBundle_pages_edit_unpublish")
     * @Template()
     * @return RedirectResponse
     */
    public function unPublishAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        /* @var Node $node */
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted('UNPUBLISH', $node)) {
            throw new AccessDeniedException();
        }

        $nodeTranslation = $node->getNodeTranslation($locale, true);
        $nodeTranslation->setOnline(false);

        $editCommand = new EditCommand($em, $user);
        $editCommand->execute('unpublished page "' . $nodeTranslation->getTitle() . '" for locale: ' . $locale, array('entity' => $nodeTranslation));

        return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', array('id' => $node->getId())));
    }

    /**
     * @param integer $id        The node id
     * @param string  $subAction The subaction (draft|public)
     *
     * @Route("/{id}/{subaction}", requirements={"_method" = "GET|POST", "id" = "\d+"}, defaults={"subaction" = "public"}, name="KunstmaanAdminNodeBundle_pages_edit")
     * @Template()
     *
     * @return RedirectResponse|array
     */
    public function editAction($id, $subAction)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        /* @var AclHelper $aclHelper */
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

        /* @var Node $node */
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        // Check with Acl
        if (false === $securityContext->isGranted('EDIT', $node)) {
            throw new AccessDeniedException();
        }

        // Force draft subaction when user has no publish rights
        if (false === $securityContext->isGranted('PUBLISH', $node)) {
            $subAction = 'draft';
        }

        $draft = ($subAction == 'draft');

        $nodeTranslation = $node->getNodeTranslation($locale, true);
        if (!$nodeTranslation) {
            $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, $node, 'EDIT', true, true);

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
            $subAction = "draft";
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
            if (false === $securityContext->isGranted('DELETE', $node)) {
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

        $formFactory = $this->container->get('form.factory');
        $formBuilder = $this->createFormBuilder();

        $seo = $nodeTranslation->getSEO();

        //add the specific data from the custom page
        $formBuilder->add('main', $page->getDefaultAdminType());
        $formBuilder->add('node', $node->getDefaultAdminType($this->container));
        $formBuilder->add('nodetranslation', $nodeTranslation->getDefaultAdminType($this->container));
        $formBuilder->add('seo', new SEOType());

        $bindingarray = array('node' => $node, 'main' => $page, 'nodetranslation'=> $nodeTranslation, 'seo' => $seo);
        if (method_exists($page, 'getExtraAdminTypes')) {
            foreach ($page->getExtraAdminTypes() as $key => $admintype) {
                $formBuilder->add($key, $admintype);
                $bindingarray[$key] = $page;
            }
        }
        $formBuilder->setData($bindingarray);

        //handle the pagepart functions (fetching, change form to reflect all fields, assigning data, etc...)
        $pagepartadmins = array();
        if (method_exists($page, 'getPagePartAdminConfigurations')) {
            foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                $pagepartadmin = $this->get('pagepartadmin.factory')->createList($pagePartAdminConfiguration, $em, $page, null, $this->container);
                $pagepartadmin->preBindRequest($request);
                $pagepartadmin->adaptForm($formBuilder, $formFactory);
                $pagepartadmins[] = $pagepartadmin;
            }
        }

        if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
            $permissionadmin = $this->container->get('admin.permissionadmin');
            // @todo Fetch permissionmap from page?
            $permissionMap = $this->container->get('security.acl.permission.map');
            $shellHelper = $this->container->get('admin_node.shell_helper');
            $permissionadmin->initialize($node, $permissionMap, $shellHelper);
        }
        $form = $formBuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            foreach ($pagepartadmins as $pagepartadmin) {
                $pagepartadmin->bindRequest($request);
            }
            if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
                $permissionadmin->bindRequest($request);
            }
            if ($form->isValid()) {
                foreach ($pagepartadmins as $pagepartadmin) {
                    $pagepartadmin->postBindRequest($request);
                }
                $nodeTranslation->setTitle($page->getTitle());
                $em->persist($node);
                $em->persist($nodeTranslation);

                $editcommand = new EditCommand($em, $user);
                $editcommand->execute('added pageparts to page "' . $page->getTitle() . '" with locale: ' . $locale, array('entity' => $page));

                if (is_string($saveAndPublish) && $saveAndPublish != '') {
                    $newpublicpage = $page->deepClone($em);
                    $nodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($newpublicpage, $nodeTranslation, $user, 'public');
                    $nodeTranslation->setPublicNodeVersion($nodeVersion);
                    $nodeTranslation->setTitle($newpublicpage->getTitle());
                    $nodeTranslation->setOnline(true);
                    $addcommand = new AddCommand($em, $user);
                    $addcommand->execute('saved and published page "' . $nodeTranslation->getTitle() . '" added with locale: ' . $locale, array('entity' => $nodeTranslation));
                    $draft = false;
                    $subAction = 'public';
                }

                $this->get('event_dispatcher')->dispatch(Events::POSTEDIT, new PageEvent($node, $nodeTranslation, $page));

                $redirectparams = array(
                    'id' => $node->getId(),
                    'subaction' => $subAction,
                    'currenttab' => $currentTab,
                    );
                if (isset($editPagePart)) {
                    $redirectparams['edit'] = $editPagePart;
                }

                return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', $redirectparams));
            }
        }

        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, $node, 'EDIT', true, true);
        $topNodes    = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, 'EDIT', $aclHelper);
        $viewVariables = array(
            'topnodes'          => $topNodes,
            'page'              => $page,
            'entityname'        => ClassLookup::getClass($page),
            'form'              => $form->createView(),
            'pagepartadmins'    => $pagepartadmins,
            'nodeVersions'      => $nodeVersions,
            'nodemenu'          => $nodeMenu,
            'node'              => $node,
            'nodeTranslation'   => $nodeTranslation,
            'draft'             => $draft,
            'draftNodeVersion'  => $draftNodeVersion,
            'subaction'         => $subAction,
            'currenttab'	=> $currentTab,
        );
        if ($securityContext->isGranted('ROLE_PERMISSIONMANAGER')) {
            $viewVariables['permissionadmin'] = $permissionadmin;
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
        $newpage = new $pageType();

        if (is_string($pageTitle) && $pageTitle != '') {
            $newpage->setTitle($pageTitle);
        } else {
            $newpage->setTitle('New page');
        }

        $addcommand = new AddCommand($em, $user);
        $addcommand->execute('page "' . $newpage->getTitle() .'" added with locale: ' . $locale, array('entity'=> $newpage));

        $nodeparent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($parentPage);
        $newpage->setParent($parentPage);

        $nodenewpage = $em->getRepository('KunstmaanAdminNodeBundle:Node')->createNodeFor($newpage, $locale, $user);
        $em->persist($nodenewpage);
        $em->flush();

        $aclProvider = $this->container->get('security.acl.provider');

        $parentIdentity = ObjectIdentity::fromDomainObject($nodeparent);
        $parentAcl = $aclProvider->findAcl($parentIdentity);

        $newIdentity = ObjectIdentity::fromDomainObject($nodenewpage);
        $newAcl = $aclProvider->createAcl($newIdentity);

        $aces = $parentAcl->getObjectAces();
        foreach ($aces as $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                $newAcl->insertObjectAce($securityIdentity, $ace->getMask());
            }
        }
        $aclProvider->updateAcl($newAcl);

        return $nodenewpage;
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
            $updatecommand = new EditCommand($em, $user);
            $updatecommand->execute('deleted child for page "' . $page->getTitle() . '" with locale: ' . $locale, array('entity'=> $child));
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
    public function movenodesAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();

        $parentid = $request->get('parentid');
        $parent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($parentid);

        $fromposition = $request->get('fromposition');
        $afterposition = $request->get('afterposition');

        foreach ($parent->getChildren() as $child) {
            if ($child->getSequencenumber() == $fromposition) {
                if ($child->getSequencenumber() > $afterposition) {
                    $child->setSequencenumber($afterposition + 1);
                    $em->persist($child);
                } else {
                    $child->setSequencenumber($afterposition);
                    $em->persist($child);
                }
            } else {
                if ($child->getSequencenumber() > $fromposition && $child->getSequencenumber() <= $afterposition) {
                    $newpos = $child->getSequencenumber()-1;
                    $child->setSequencenumber($newpos);
                    $em->persist($child);
                } else {
                    if ($child->getSequencenumber() < $fromposition && $child->getSequencenumber() > $afterposition) {
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
    public function ckselectlinkAction()
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
