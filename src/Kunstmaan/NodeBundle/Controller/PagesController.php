<?php

namespace Kunstmaan\AdminNodeBundle\Controller;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Kunstmaan\AdminNodeBundle\Helper\Event\Events;
use Kunstmaan\AdminNodeBundle\Helper\Event\PageEvent;

use Kunstmaan\AdminBundle\Modules\PrepersistListener;
use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;
use Kunstmaan\AdminBundle\Form\PageAdminType;
use Kunstmaan\AdminNodeBundle\AdminList\PageAdminListConfigurator;
use Kunstmaan\AdminBundle\Form\NodeInfoAdminType;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\AdminBundle\Entity\Permission;
use Kunstmaan\AdminBundle\Modules\Slugifier;
use Kunstmaan\AdminNodeBundle\Form\SEOType;
use Kunstmaan\AdminBundle\Entity\AddCommand;
use Kunstmaan\AdminBundle\Entity\EditCommand;
use Kunstmaan\AdminBundle\Entity\DeleteCommand;

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
        $user = $this->container->get('security.context')->getToken()->getUser();
        $topnodes = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, $user, 'write', true);
        $nodeMenu = new NodeMenu($this->container, $locale, null, 'write', true, true);

        $request    = $this->getRequest();
        $adminlist  = $this->get("adminlist.factory")->createList(new PageAdminListConfigurator($user, 'write', $locale), $em);
        $adminlist->bindRequest($request);

        return array(
            'topnodes'  => $topnodes,
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
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
        $otherLanguageNodeTranslation = $node->getNodeTranslation($otherlanguage, true);
        $otherLanguagePage = $otherLanguageNodeTranslation->getPublicNodeVersion()->getRef($em);
        $myLanguagePage = $otherLanguagePage->deepClone($em);
        $node = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $locale, $node, $user);

        return $this->redirect($this->generateUrl("KunstmaanAdminNodeBundle_pages_edit", array('id'=>$id)));
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
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
        $entityname = $node->getRefEntityname();
        $myLanguagePage = new $entityname();
        $myLanguagePage->setTitle("New page");

        $addcommand = new AddCommand($em, $user);
        $addcommand->execute("empty page added with locale: " . $locale, array('entity'=> $myLanguagePage));

        $node = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $locale, $node, $user);

        return $this->redirect($this->generateUrl("KunstmaanAdminNodeBundle_pages_edit", array('id'=>$id)));
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
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
        $nodeTranslation = $node->getNodeTranslation($locale, true);
        $nodeTranslation->setOnline(true);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $editcommand = new EditCommand($em, $user);
        $editcommand->execute("published page \"" . $nodeTranslation->getTitle() . "\" on locale: " . $locale, array('entity'=> $nodeTranslation));

        return $this->redirect($this->generateUrl("KunstmaanAdminNodeBundle_pages_edit", array('id'=>$node->getId())));
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
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
        $nodeTranslation = $node->getNodeTranslation($locale, true);
        $nodeTranslation->setOnline(false);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $editcommand = new EditCommand($em, $user);
        $editcommand->execute("unpublished page \"" . $nodeTranslation->getTitle() . "\" on locale: " . $locale, array('entity'=> $nodeTranslation));

        return $this->redirect($this->generateUrl("KunstmaanAdminNodeBundle_pages_edit", array('id'=>$node->getId())));
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
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();

        $saveasdraft = $request->get("saveasdraft");
        $saveandpublish = $request->get("saveandpublish");

        $draft = ($subaction == "draft");

        if ($request->request->get("currenttab")) {
            $currenttab = $request->request->get("currenttab");
        } else if ($request->get("currenttab")) {
            $currenttab = $request->get("currenttab");
        } else {
            $currenttab = 'pageparts1';
        }

        if ($request->get("edit")) {
            $editpagepart = $request->get("edit");
        }

        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);

        $guestGroup = $em->getRepository('KunstmaanAdminBundle:Group')->findOneByName('Guests');
        $guestPermission = $em->getRepository('KunstmaanAdminBundle:Permission')->findOneBy(array('refId' => $node->getId(), 'refEntityname' => ClassLookup::getClass($node), 'refGroup' => $guestGroup->getId()));
        if (is_null($guestPermission)) {
            $guestPermission = new Permission();
            $guestPermission->setRefId($node->getId());
            $guestPermission->setRefEntityname(ClassLookup::getClass($node));
            $guestPermission->setRefGroup($guestGroup);
            $guestPermission->setPermissions('|read:1|write:0|delete:0|');
            $em->persist($guestPermission);
            $em->flush();
        }

        $nodeTranslation = $node->getNodeTranslation($locale, true);
        if (!$nodeTranslation) {
            return $this->render('KunstmaanAdminNodeBundle:Pages:pagenottranslated.html.twig', array('node' => $node, 'nodeTranslations' => $node->getNodeTranslations(true), 'nodemenu' => new NodeMenu($this->container, $locale, $node, 'write', true, true)));
        }

        $nodeVersions = $nodeTranslation->getNodeVersions();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $draftNodeVersion = $nodeTranslation->getNodeVersion('draft');

        $page = $em->getRepository($nodeVersion->getRefEntityname())->find($nodeVersion->getRefId());
        if (!is_null($this->getRequest()->get('version'))) {
            $repo->revert($page, $this->getRequest()->get('version'));
        }

        if ($draft) {
            $nodeVersion = $draftNodeVersion;
            $page = $nodeVersion->getRef($em);
        } else {
            if (is_string($saveasdraft) && $saveasdraft != '') {
                $publicpage = $page->deepClone($em);
                $publicnodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($publicpage, $nodeTranslation, $user, 'public');
                $nodeTranslation->setPublicNodeVersion($publicnodeVersion);
                $nodeVersion->setType('draft');
                $em->persist($nodeTranslation);
                $em->persist($nodeVersion);
                $draft = true;
                $subaction = "draft";
            }
        }

        $this->get('admin_node.actions_menu_builder')->setActiveNodeVersion($nodeVersion);

        $addpage = $request->get("addpage");
        $addpagetitle = $request->get("addpagetitle");
        if (is_string($addpage) && $addpage != '') {
            $nodenewpage = $this->addPage($em, $user, $locale, $page, $addpage, $addpagetitle);

            return $this->redirect($this->generateUrl("KunstmaanAdminNodeBundle_pages_edit", array('id' => $nodenewpage->getId(), 'currenttab' => $currenttab)));
        }

        $delete = $request->get("delete");
        if (is_string($delete) && $delete == 'true') {
            //remove node and page
            $nodeparent = $node->getParent();
            $node->setDeleted(true);
            $updatecommand = new EditCommand($em, $user);
            $updatecommand->execute("deleted page \"". $page->getTitle() ."\" with locale: " . $locale, array('entity'=> $node));
            $children = $node->getChildren();
            $this->deleteNodeChildren($em, $user, $locale, $children, $page);

            return $this->redirect($this->generateUrl("KunstmaanAdminNodeBundle_pages_edit", array('id'=>$nodeparent->getId(), 'currenttab' => $currenttab)));
        }

        $topnodes   = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, $user, 'write');

        $formfactory = $this->container->get('form.factory');
        $formbuilder = $this->createFormBuilder();

        $seo = $nodeTranslation->getSEO();

        //add the specific data from the custom page
        $formbuilder->add('main', $page->getDefaultAdminType());
        $formbuilder->add('node', $node->getDefaultAdminType($this->container));
        $formbuilder->add('nodetranslation', $nodeTranslation->getDefaultAdminType($this->container));
        $formbuilder->add('seo', new SEOType());

        $bindingarray = array('node' => $node, 'main' => $page, 'nodetranslation'=> $nodeTranslation, 'seo' => $seo);
        if (method_exists($page, "getExtraAdminTypes")) {
            foreach ($page->getExtraAdminTypes() as $key => $admintype) {
                $formbuilder->add($key, $admintype);
                $bindingarray[$key] = $page;
            }
        }
        $formbuilder->setData($bindingarray);

        //handle the pagepart functions (fetching, change form to reflect all fields, assigning data, etc...)
        $pagepartadmins = array();
        if (method_exists($page, "getPagePartAdminConfigurations")) {
            foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                $pagepartadmin = $this->get("pagepartadmin.factory")->createList($pagePartAdminConfiguration, $em, $page, null, $this->container);
                $pagepartadmin->preBindRequest($request);
                $pagepartadmin->adaptForm($formbuilder, $formfactory);
                $pagepartadmins[] = $pagepartadmin;
            }
        }

        if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
            $permissionadmin = $this->get("kunstmaan_admin.permissionadmin");
            $permissionadmin->initialize($node, $em, $page->getPossiblePermissions());
        }
        $form = $formbuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
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

                $formValues = $request->request->get('form');
                if (isset($formValues['node']['roles'])) {
                    $roles = array_keys($formValues['node']['roles']);
                } else {
                    $roles = array();
                }

                $node->setRoles($roles);
                $nodeTranslation->setTitle($page->getTitle());
                $em->persist($node);
                $em->persist($nodeTranslation);
                
                $editcommand = new EditCommand($em, $user);
                $editcommand->execute("added pageparts to page \"". $page->getTitle() ."\" with locale: " . $locale, array('entity'=> $page));
                
                if (is_string($saveandpublish) && $saveandpublish != '') {
                    $newpublicpage = $page->deepClone($em);
                    $nodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($newpublicpage, $nodeTranslation, $user, 'public');
                    $nodeTranslation->setPublicNodeVersion($nodeVersion);
                    $nodeTranslation->setTitle($newpublicpage->getTitle());
                    $nodeTranslation->setOnline(true);
                    $addcommand = new AddCommand($em, $user);
                    $addcommand->execute("saved and published page \"". $nodeTranslation->getTitle() ."\" added with locale: " . $locale, array('entity'=> $nodeTranslation));
                    $draft = false;
                    $subaction = "public";
                }

                $this->get('event_dispatcher')->dispatch(Events::POSTEDIT, new PageEvent($node, $nodeTranslation, $page));

                $redirectparams = array(
                    'id' => $node->getId(),
                    'subaction' => $subaction,
                    'currenttab' => $currenttab,
                    );
                if (isset($editpagepart)) {
                    $redirectparams['edit'] = $editpagepart;
                }

                return $this->redirect($this->generateUrl('KunstmaanAdminNodeBundle_pages_edit', $redirectparams));
            }
        }

        $nodeMenu = new NodeMenu($this->container, $locale, $node, 'write', true, true);

        $viewVariables = array(
            'topnodes'          => $topnodes,
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
            'subaction'         => $subaction,
            'currenttab'		=> $currenttab,
        );
        if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
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
        $addcommand->execute("page \"". $newpage->getTitle() ."\" added with locale: " . $locale, array('entity'=> $newpage));

        $nodeparent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($parentPage);
        $newpage->setParent($parentPage);

        $nodenewpage = $em->getRepository('KunstmaanAdminNodeBundle:Node')->createNodeFor($newpage, $locale, $user);

        //get permissions of the parent and apply them on the new child
        $parentPermissions = $em->getRepository('KunstmaanAdminBundle:Permission')->findBy(array(
            'refId'             => $nodeparent->getId(),
            'refEntityname'     => ClassLookup::getClass($nodeparent),
        ));

        if ($parentPermissions) {
            foreach ($parentPermissions as $parentPermission) {
                $permission = new Permission();

                $permission->setRefId($nodenewpage->getId());
                $permission->setPermissions($parentPermission->getPermissions());
                $permission->setRefEntityname(ClassLookup::getClass($nodeparent));
                $permission->setRefGroup($parentPermission->getRefGroup());

                $em->persist($permission);
                $em->flush();
            }
        }

        $em->persist($nodenewpage);
        $em->flush();

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
            $updatecommand->execute("deleted child for page \"". $page->getTitle() ."\" with locale: " . $locale, array('entity'=> $child));
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

        return array("success" => true);
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
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $topnodes = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, $user, 'read');
        $nodeMenu = new NodeMenu($this->container, $locale, null, 'read', true, true);

        return array(
            'topnodes'    => $topnodes,
            'nodemenu'    => $nodeMenu,
        );
    }

}
