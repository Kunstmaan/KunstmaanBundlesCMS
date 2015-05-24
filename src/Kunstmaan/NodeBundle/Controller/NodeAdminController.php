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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @var SecurityContextInterface $securityContext
     */
    protected $securityContext;

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
     */
    protected function init()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->locale = $this->getRequest()->getLocale();
        $this->securityContext = $this->container->get('security.context');
        $this->user = $this->securityContext->getToken()->getUser();
        $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
    }

    /**
     * @Route("/", name="KunstmaanNodeBundle_nodes")
     * @Template("KunstmaanNodeBundle:Admin:list.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $this->init();

        /* @var AdminList $adminlist */
        $adminlist = $this->get('kunstmaan_adminlist.factory')->createList(new NodeAdminListConfigurator($this->em, $this->aclHelper, $this->locale, PermissionMap::PERMISSION_EDIT));
        $adminlist->bindRequest($this->getRequest());

        return array(
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
        $this->em->flush();
        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $this->locale, $node, $this->user);
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(Events::ADD_EMPTY_PAGE_TRANSLATION, new NodeEvent($node, $nodeTranslation, $nodeVersion, $myLanguagePage));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $id)));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/publish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_publish")
     *
     * @return RedirectResponse
     */
    public function publishAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $request = $this->get('request');

        if ($request->get('pub_date')) {
            $date = new \DateTime($request->get('pub_date') . ' ' . $request->get('pub_time'));
            $this->get('kunstmaan_node.admin_node.publisher')->publishLater($nodeTranslation, $date);
            $this->get('session')->getFlashBag()->add('success', 'Publishing of the page has been scheduled');
        } else {
            $this->get('kunstmaan_node.admin_node.publisher')->publish($nodeTranslation);
            $this->get('session')->getFlashBag()->add('success', 'The page has been published');
        }

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $node->getId())));
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/unpublish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_unpublish")
     *
     * @return RedirectResponse
     */
    public function unPublishAction($id)
    {
        $this->init();
        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $request = $this->get('request');

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
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/unschedulepublish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_unschedule_publish")
     *
     * @return RedirectResponse
     */
    public function unSchedulePublishAction($id)
    {
        $this->init();

        /* @var Node $node */
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        $this->get('kunstmaan_node.admin_node.publisher')->unSchedulePublish($nodeTranslation);

        $this->get('session')->getFlashBag()->add('success', 'The scheduling has been canceled');

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $id)));
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

        $node->setDeleted(true);
        $this->em->persist($node);

        $children = $node->getChildren();
        $this->deleteNodeChildren($this->em, $this->user, $this->locale, $children);
        $this->em->flush();

        $event = new NodeEvent($node, $nodeTranslation, $nodeVersion, $page);
        $this->get('event_dispatcher')->dispatch(Events::POST_DELETE, $event);
        if (null === $response = $event->getResponse()) {
            $nodeParent = $node->getParent();
            $url = $this->container->get('router')->generate('KunstmaanNodeBundle_nodes_edit', array('id' => $nodeParent->getId()));
            $response = new RedirectResponse($url);
        }

        $this->get('session')->getFlashBag()->add('success', 'The page is deleted!');

        return $response;
    }

    /**
     * @param int $id
     *
     * @throws AccessDeniedException
     * @Route("/{id}/duplicate", requirements={"_method" = "POST", "id" = "\d+"}, name="KunstmaanNodeBundle_nodes_duplicate")
     * @Template()
     *
     * @return RedirectResponse
     */
    public function duplicateAction($id)
    {
        $this->init();
        /* @var Node $parentNode */
        $originalNode = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        // Check with Acl
        $this->checkPermission($originalNode, PermissionMap::PERMISSION_EDIT);

        $request = $this->get('request');

        $originalNodeTranslations = $originalNode->getNodeTranslation($this->locale, true);
        $originalRef = $originalNodeTranslations->getPublicNodeVersion()->getRef($this->em);
        $newPage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($originalRef);

        //set the title
        $title = $request->get('title');
        if (is_string($title) && !empty($title)) {
            $newPage->setTitle($title);
        } else {
            $newPage->setTitle('New page');
        }

        //set the parent
        $parentNodeTranslation = $originalNode->getParent()->getNodeTranslation($this->locale,true);
        $parent = $parentNodeTranslation->getPublicNodeVersion()->getRef($this->em);

        $newPage->setParent($parent);

        $this->em->persist($newPage);
        $this->em->flush();

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository('KunstmaanNodeBundle:Node')->createNodeFor($newPage, $this->locale, $this->user);

        $nodeTranslation = $nodeNewPage->getNodeTranslation($this->locale, true);
        if ($newPage->isStructureNode()) {
            $nodeTranslation->setSlug('');
            $this->em->persist($nodeTranslation);
        }

        $this->em->flush();

        $this->updateAcl($originalNode, $nodeNewPage);

        $this->get('session')->getFlashBag()->add('success', 'The page has been duplicated!');

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $nodeNewPage->getId())));
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
        $page = $nodeVersion->getRef($this->em);
        /* @var HasNodeInterface $clonedPage */
        $clonedPage = $this->get('kunstmaan_admin.clone.helper')->deepCloneAndSave($page);
        $newNodeVersion = $nodeVersionRepo->createNodeVersionFor($clonedPage, $nodeTranslation, $this->user, $nodeVersion, 'draft');

        $nodeTranslation->setTitle($clonedPage->getTitle());
        $this->em->persist($nodeTranslation);
        $this->em->flush();

        $this->get('event_dispatcher')->dispatch(Events::REVERT, new RevertNodeAction($node, $nodeTranslation, $newNodeVersion, $clonedPage, $nodeVersion, $page));

        $this->get('session')->getFlashBag()->add('success', 'The page contents has been reverted');

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
            throw new InvalidArgumentException('Please specify a type of page you want to create');
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
        $this->em->flush();

        $newPage->setParent($parentPage);

        /* @var Node $nodeNewPage */
        $nodeNewPage = $this->em->getRepository('KunstmaanNodeBundle:Node')->createNodeFor($newPage, $this->locale, $this->user);
        $nodeTranslation = $nodeNewPage->getNodeTranslation($this->locale, true);
        if ($newPage->isStructureNode()) {
            $nodeTranslation->setSlug('');
            $this->em->persist($nodeTranslation);
        }

        $this->em->flush();

        $this->updateAcl($parentNode, $nodeNewPage);

        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->get('event_dispatcher')->dispatch(Events::ADD_NODE, new NodeEvent($nodeNewPage, $nodeTranslation, $nodeVersion, $newPage));

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', array('id' => $nodeNewPage->getId())));
    }

    /**
     * @param Request $request
     * @throws AccessDeniedException
     *
     * @Route("/reorder", requirements={"_method" = "POST"}, name="KunstmaanNodeBundle_nodes_reorder")
     *
     * @return string
     */
    public function reorderAction(Request $request)
    {
        $this->init();
        $nodes = array();
        $nodeIds = $request->get('nodes');

        foreach($nodeIds as $id){
            /* @var Node $node */
            $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
            $this->checkPermission($node, PermissionMap::PERMISSION_EDIT);
            $nodes[] = $node;
        }

        $weight = 0;
        foreach($nodes as $node){

            /* @var NodeTranslation $nodeTranslation */
            $nodeTranslation = $node->getNodeTranslation($this->locale, true);
            $nodeVersion = $nodeTranslation->getPublicNodeVersion();
            $page = $nodeVersion->getRef($this->em);

            $this->get('event_dispatcher')->dispatch(Events::PRE_PERSIST, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

            $nodeTranslation->setWeight($weight);
            $this->em->persist($nodeTranslation);
            $this->em->flush($nodeTranslation);

            $this->get('event_dispatcher')->dispatch(Events::POST_PERSIST, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

            $weight++;
        }

        return new JsonResponse(array(
            'Success' => 'The node-translations for ['.$this->locale.'] have got new weight values'
        ));
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
        $tabPane = new TabPane('todo', $request, $this->container->get('form.factory'));

        $nodeTranslation = $node->getNodeTranslation($this->locale, true);
        if (!$nodeTranslation) {
            //try to find a parent node with the correct translation, if there is none allow copy.
            //if there is a parent but it doesn't have the language to copy to don't allow it
            $parentNode = $node->getParent();
            if ($parentNode) {
                $parentNodeTranslation = $parentNode->getNodeTranslation($this->locale, true);
                $parentsAreOk = false;

                if ($parentNodeTranslation) {
                    $parentsAreOk = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->hasParentNodeTranslationsForLanguage($node->getParent()->getNodeTranslation($this->locale, true), $this->locale);
                }
            } else {
                $parentsAreOk = true;
            }

            return $this->render('KunstmaanNodeBundle:NodeAdmin:pagenottranslated.html.twig', array(
                'node' => $node,
                'nodeTranslations' => $node->getNodeTranslations(true),
                'copyfromotherlanguages' => $parentsAreOk
            ));
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
            if ($request->getMethod() == 'POST') {
                //Check the version timeout and make a new nodeversion if the timeout is passed
                $thresholdDate = date("Y-m-d H:i:s", time()-$this->container->getParameter("kunstmaan_node.version_timeout"));
                $updatedDate = date("Y-m-d H:i:s", strtotime($nodeVersion->getUpdated()->format("Y-m-d H:i:s")));
                if ($thresholdDate >= $updatedDate) {
                    $page = $nodeVersion->getRef($this->em);
                    if ($nodeVersion == $nodeTranslation->getPublicNodeVersion()) {
                        $this->get('kunstmaan_node.admin_node.publisher')->createPublicVersion($page, $nodeTranslation, $nodeVersion, $this->user);
                    } else {
                        $this->createDraftVersion($page, $nodeTranslation, $nodeVersion);
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
        $pageAdminType = $page->getDefaultAdminType();
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
        if (!$isStructureNode) {
            $menuWidget = new FormWidget();
            $menuWidget->addType('menunodetranslation', new NodeMenuTabTranslationAdminType(), $nodeTranslation);
            $menuWidget->addType('menunode', new NodeMenuTabAdminType(), $node);
            $tabPane->addTab(new Tab('Menu', $menuWidget));

            $this->get('event_dispatcher')->dispatch(Events::ADAPT_FORM, new AdaptFormEvent($request, $tabPane, $page, $node, $nodeTranslation, $nodeVersion));
        }
        $tabPane->buildForm();

        if ($request->getMethod() == 'POST') {
            $tabPane->bindRequest($request);

            if ($tabPane->isValid()) {
                $this->get('event_dispatcher')->dispatch(Events::PRE_PERSIST, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

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

                $this->get('event_dispatcher')->dispatch(Events::POST_PERSIST, new NodeEvent($node, $nodeTranslation, $nodeVersion, $page));

                $this->get('session')->getFlashBag()->add('success', 'The page has been edited');

                $params = array(
                    'id' => $node->getId(),
                    'subaction' => $subaction,
                    'currenttab' => $tabPane->getActiveTab()
                );
                $params = array_merge($params, $tabPane->getExtraParams($request));

                return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', $params));
            }
        }

        $nodeVersions = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->findBy(array('nodeTranslation' => $nodeTranslation), array('updated'=> 'ASC'));
        $queuedNodeTranslationAction = $this->em->getRepository('KunstmaanNodeBundle:QueuedNodeTranslationAction')->findOneBy(array('nodeTranslation' => $nodeTranslation));

        return array(
            'page' => $page,
            'entityname' => ClassLookup::getClass($page),
            'nodeVersions' => $nodeVersions,
            'node' => $node,
            'nodeTranslation' => $nodeTranslation,
            'draft' => $draft,
            'draftNodeVersion' => $draftNodeVersion,
            'subaction' => $subaction,
            'tabPane' => $tabPane,
            'editmode' => true,
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
     * @param BaseUser        $user     The user who deletes the children
     * @param string          $locale   The locale that was used
     * @param ArrayCollection $children The children array
     */
    private function deleteNodeChildren(EntityManager $em, BaseUser $user, $locale, ArrayCollection $children)
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

    /**
     * @param $originalNode
     * @param $nodeNewPage
     */
    private function updateAcl($originalNode, $nodeNewPage)
    {
        /* @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $this->container->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $strategy */
        $strategy = $this->container->get('security.acl.object_identity_retrieval_strategy');
        $originalIdentity = $strategy->getObjectIdentity($originalNode);
        $originalAcl = $aclProvider->findAcl($originalIdentity);

        $newIdentity = $strategy->getObjectIdentity($nodeNewPage);
        $newAcl = $aclProvider->createAcl($newIdentity);

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

}
