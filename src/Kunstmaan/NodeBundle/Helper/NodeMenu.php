<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Repository\NodeRepository;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * NodeMenu
 */
class NodeMenu
{

    /***** constructor arguments *****/

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var AclHelper
     */
    private $aclHelper;

    /**
     * @var string
     */
    private $lang;

    /**
     * @var Node
     */
    private $currentNode = null;

    /**
     * @var string
     */
    private $permission = null;

    /**
     * @var bool
     */
    private $includeOffline = false;

    /**
     * @var bool
     */
    private $includeHiddenFromNav = false;


    /***** temporary storage variables *****/

    /**
     * @var NodeMenuItem[]
     */
    private $topNodeMenuItems = null;

    /**
     * @var NodeMenuItem[]
     */
    private $breadCrumb = null;

    /**
     * @var Node[]
     */
    private $allNodes = array();

    /**
     * @var Node[]
     */
    private $childNodes = array();

    /**
     * @var Node[]
     */
    private $nodesByInternalName = array();

    /**
     * @param EntityManager            $em                   The entity manager
     * @param SecurityContextInterface $securityContext      The security context
     * @param AclHelper                $aclHelper            The ACL helper
     * @param string                   $lang                 The language
     * @param Node|null                $currentNode          The node
     * @param string                   $permission           The permission
     * @param bool                     $includeOffline       Include offline pages
     * @param bool                     $includeHiddenFromNav Include hidden pages
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclHelper $aclHelper, $lang, Node $currentNode = null, $permission = PermissionMap::PERMISSION_VIEW, $includeOffline = false, $includeHiddenFromNav = false)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->aclHelper = $aclHelper;
        $this->lang = $lang;
        $this->includeOffline = $includeOffline;
        $this->includeHiddenFromNav = $includeHiddenFromNav;
        $this->permission = $permission;
        $this->currentNode = $currentNode;

        /* @var NodeRepository $repo */
        $repo = $this->em->getRepository('KunstmaanNodeBundle:Node');

        // Get all possible menu items in one query (also fetch offline nodes)
        $nodes = $repo->getChildNodes(false, $this->lang, $permission, $this->aclHelper, true);
        foreach ($nodes as $node) {
            $this->allNodes[$node->getId()] = $node;

            if ($node->getParent()) {
                $this->childNodes[$node->getParent()->getId()][] = $node;
            } else {
                $this->childNodes[0][] = $node;
            }

            if ($node->getInternalName()) {
                $this->nodesByInternalName[$node->getInternalName()][] = $node;
            }
        }
    }

    /**
     * @return NodeMenuItem[]
     */
    public function getTopNodes()
    {
        if (!is_array($this->topNodeMenuItems)) {
            $this->topNodeMenuItems = array();

            // To be backwards compatible we need to create the top node MenuItems
            if (array_key_exists(0, $this->childNodes)) {
                $topNodeMenuItems = array();
                $topNodes = $this->childNodes[0];
                /* @var Node $topNode */
                foreach ($topNodes as $topNode) {
                    $nodeTranslation = $topNode->getNodeTranslation($this->lang, $this->includeOffline);
                    if (!is_null($nodeTranslation)) {
                        $topNodeMenuItems[] = new NodeMenuItem($topNode, $nodeTranslation, false, $this);
                    }
                }

                $includeHiddenFromNav = $this->includeHiddenFromNav;
                $this->topNodeMenuItems = array_filter($topNodeMenuItems, function (NodeMenuItem $entry) use ($includeHiddenFromNav) {
                    if ($entry->getNode()->isHiddenFromNav() && !$includeHiddenFromNav) {
                        return false;
                    }

                    return true;
                });
            }
        }

        return $this->topNodeMenuItems;
    }

    /**
     * @return NodeMenuItem[]
     */
    public function getBreadCrumb()
    {
        if (!is_array($this->breadCrumb)) {
            $this->breadCrumb = array();

            /* @var NodeRepository $repo */
            $repo = $this->em->getRepository('KunstmaanNodeBundle:Node');

            // Generate breadcrumb MenuItems - fetch *all* languages so you can link translations if needed
            $parentNodes = $repo->getAllParents($this->currentNode);
            $parentNodeMenuItem = null;
            /* @var Node $parentNode */
            foreach ($parentNodes as $parentNode) {
                $nodeTranslation = $parentNode->getNodeTranslation($this->lang, $this->includeOffline);
                if (!is_null($nodeTranslation)) {
                    $nodeMenuItem = new NodeMenuItem($parentNode, $nodeTranslation, $parentNodeMenuItem, $this);
                    $this->breadCrumb[] = $nodeMenuItem;
                    $parentNodeMenuItem = $nodeMenuItem;
                }
            }
        }

        return $this->breadCrumb;
    }

    /**
     * @return NodeMenuItem|null
     */
    public function getCurrent()
    {
        $breadCrumb = $this->getBreadCrumb();
        if (count($breadCrumb) > 0) {
            return $breadCrumb[count($breadCrumb)-1];
        }

        return null;
    }

    /**
     * @param int $depth
     *
     * @return NodeMenuItem|null
     */
    public function getActiveForDepth($depth)
    {
        $breadCrumb = $this->getBreadCrumb();
        if (count($breadCrumb) >= $depth) {
            return $breadCrumb[$depth-1];
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return NodeMenuItem|NULL
     */
    private function getBreadCrumbItemByNode(Node $node)
    {
        foreach ($this->getBreadCrumb() as $breadCrumbItem) {
            if ($breadCrumbItem->getNode()->getId() == $node->getId()) {
                return $breadCrumbItem;
            }
        }

        return null;
    }

    /**
     * @param Node $node
     * @param bool $includeHiddenFromNav
     *
     * @return NodeMenuItem[]
     */
    public function getChildren(Node $node, $includeHiddenFromNav = true)
    {
        $children = array();

        if (array_key_exists($node->getId(), $this->childNodes)) {
            $nodes = $this->childNodes[$node->getId()];
            /* @var Node $node */
            foreach ($nodes as $node) {
                $nodeTranslation = $node->getNodeTranslation($this->lang, $this->includeOffline);
                if (!is_null($nodeTranslation)) {
                    $children[] = new NodeMenuItem($node, $nodeTranslation, null, $this);
                }
            }

            $children = array_filter($children, function (NodeMenuItem $entry) use ($includeHiddenFromNav) {
                if ($entry->getNode()->isHiddenFromNav() && !$includeHiddenFromNav) {
                    return false;
                }

                return true;
            });
        }

        return $children;
    }

    /**
     * @param NodeTranslation $parentNode The parent node
     * @param string          $slug       The slug
     *
     * @return NodeTranslation
     */
    public function getNodeBySlug(NodeTranslation $parentNode, $slug)
    {
        return $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getNodeTranslationForSlug($slug, $parentNode);
    }

    /**
     * @param string                                        $internalName The internal name
     * @param NodeTranslation|NodeMenuItem|HasNodeInterface $parent       The parent
     * @param bool                                          $includeOffline
     *
     * @return NodeMenuItem|null
     */
    public function getNodeByInternalName($internalName, $parent = null, $includeOffline = null)
    {
        $repo = $this->em->getRepository('KunstmaanNodeBundle:Node');
        $node = null;
        if ($includeOffline == null) {
            $includeOffline = $this->includeOffline;
        }

        if (!is_null($parent)) {
            if ($parent instanceof NodeTranslation) {
                $parent = $parent->getNode();
            } elseif ($parent instanceof NodeMenuItem) {
                $parent = $parent->getNode();
            } elseif ($parent instanceof HasNodeInterface) {
                $parent = $repo->getNodeFor($parent);
            }

            $nodes = $repo->getNodesByInternalName($internalName, $this->lang, $parent->getId(), $includeOffline);
            if (count($nodes) > 0) {
                $node = $nodes[0];
            } else {
                $nodes = $repo->getNodesByInternalName($internalName, $this->lang, false, $includeOffline);
                /* @var Node $n */
                foreach ($nodes as $n) {
                    $p = $n;
                    while (is_null($node) && !is_null($p->getParent())) {
                        $pParent = $p->getParent();
                        if ($pParent->getId() == $parent->getId()) {
                            $node = $n;
                            break;
                        }
                        $p = $pParent;
                    }
                }
            }
        } else {
            $nodes = $repo->getNodesByInternalName($internalName, $this->lang, false, $includeOffline);
            if (count($nodes) == 1) {
                $node = $nodes[0];
            }
        }

        if (!is_null($node)) {
            $nodeTranslation = $node->getNodeTranslation($this->lang, $includeOffline);
            if (!is_null($nodeTranslation)) {
                return $this->getNodemenuForNodeTranslation($nodeTranslation, $includeOffline);
            }
        }

        return null;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @param bool            $includeOffline
     *
     * @return NodeMenuItem|NULL
     */
    private function getNodemenuForNodeTranslation(NodeTranslation $nodeTranslation, $includeOffline = null)
    {
        if ($includeOffline == null) {
            $includeOffline = $this->includeOffline;
        }

        if (!is_null($nodeTranslation)) {
            $tempNode = $nodeTranslation->getNode();
            //Breadcrumb
            $nodeBreadCrumb = array();
            $parentNodeMenuItem = null;
            while ($tempNode && is_null($parentNodeMenuItem)) {
                array_unshift($nodeBreadCrumb, $tempNode);
                $tempNode = $tempNode->getParent();
                if (!is_null($tempNode)) {
                    $parentNodeMenuItem = $this->getBreadCrumbItemByNode($tempNode);
                }
            }
            $nodeMenuItem = null;
            /* @var Node $nodeBreadCrumbItem */
            foreach ($nodeBreadCrumb as $nodeBreadCrumbItem) {
                $breadCrumbItemFromMain = $this->getBreadCrumbItemByNode($nodeBreadCrumbItem);
                if (!is_null($breadCrumbItemFromMain)) {
                    $parentNodeMenuItem = $breadCrumbItemFromMain;
                }
                $nodeTranslation = $nodeBreadCrumbItem->getNodeTranslation($this->lang, $includeOffline);
                if (!is_null($nodeTranslation)) {
                    $nodeMenuItem = new NodeMenuItem($nodeBreadCrumbItem, $nodeTranslation, $parentNodeMenuItem, $this);
                    $parentNodeMenuItem = $nodeMenuItem;
                }
            }

            return $nodeMenuItem;
        }

        return null;
    }



    /**
     * @return bool
     */
    public function isIncludeOffline()
    {
        return $this->includeOffline;
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @return BaseUser
     */
    public function getUser()
    {
        return $this->securityContext->getToken()->getUser();
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return SecurityContextInterface
     */
    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    /**
     * @return AclHelper
     */
    public function getAclHelper()
    {
        return $this->aclHelper;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return bool
     */
    public function isIncludeHiddenFromNav()
    {
        return $this->includeHiddenFromNav;
    }

}
