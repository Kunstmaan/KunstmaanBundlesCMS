<?php

namespace Kunstmaan\AdminNodeBundle\Modules;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Translator;

use Knp\Menu\FactoryInterface;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminNodeBundle\Entity\HasNodeInterface;
use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

/**
 * NodeMenu
 */
class NodeMenu
{
    private $em;
    private $securityContext;
    private $aclHelper;    
    private $lang;
    private $topNodeMenuItems = array();
    private $breadCrumb = array();
    private $includeoffline = false;
    private $includehiddenfromnav = false;
    private $permission = null;
    private $user = null;

    /**
     * @param EntityManager             $em                   The entity manager
     * @param SecurityContextInterface  $securityContext      The security context
     * @param AclHelper                 $aclHelper            The ACL helper
     * @param string                    $lang                 The language
     * @param Node                      $currentNode          The node
     * @param string                    $permission           The permission
     * @param boolean                   $includeoffline       Include offline pages
     * @param boolean                   $includehiddenfromnav Include hidden pages
     */
    public function __construct($em, $securityContext, $aclHelper, $lang, Node $currentNode = null, $permission = 'VIEW', $includeoffline = false, $includehiddenfromnav = false)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->aclHelper = $aclHelper;
        $this->lang = $lang;
        $this->includeoffline = $includeoffline;
        $this->includehiddenfromnav = $includehiddenfromnav;
        $this->permission = $permission;
        $tempNode = $currentNode;

        //Breadcrumb
        $nodeBreadCrumb = array();
        while ($tempNode) {
            array_unshift($nodeBreadCrumb, $tempNode);
            $tempNode = $tempNode->getParent();
        }
        $parentNodeMenuItem = null;
        foreach ($nodeBreadCrumb as $nodeBreadCrumbItem) {
            $nodeTranslation = $nodeBreadCrumbItem->getNodeTranslation($this->lang, $this->includeoffline);
            if (!is_null($nodeTranslation)) {
                $nodeMenuItem = new NodeMenuItem($nodeBreadCrumbItem, $nodeTranslation, $parentNodeMenuItem, $this);
                $this->breadCrumb[] = $nodeMenuItem;
                $parentNodeMenuItem = $nodeMenuItem;
            }
        }

        $this->user = $this->securityContext->getToken()->getUser();

        //topNodes
        $topNodes = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($this->lang, $permission, $this->aclHelper, $includehiddenfromnav);
        foreach ($topNodes as $topNode) {
            $nodeTranslation = $topNode->getNodeTranslation($this->lang, $this->includeoffline);
            if (!is_null($nodeTranslation)) {
                if (sizeof($this->breadCrumb)>0 && $this->breadCrumb[0]->getNode()->getId() == $topNode->getId()) {
                    $this->topNodeMenuItems[] = $this->breadCrumb[0];
                } else {
                    $this->topNodeMenuItems[] = new NodeMenuItem($topNode, $nodeTranslation, null, $this);
                }
            }
        }
    }

    /**
     * @return NodeMenuItem[]
     */
    public function getTopNodes()
    {
        return $this->topNodeMenuItems;
    }

    /**
     * @return NodeMenuItem|NULL
     */
    public function getCurrent()
    {
        if (sizeof($this->breadCrumb)>0) {
            return $this->breadCrumb[sizeof($this->breadCrumb)-1];
        }

        return null;
    }

    /**
     * @param integer $depth
     *
     * @return NodeMenuItem|NULL
     */
    public function getActiveForDepth($depth)
    {
        if (sizeof($this->breadCrumb)>=$depth) {
            return $this->breadCrumb[$depth-1];
        }

        return null;
    }

    /**
     * @return NodeMenuItem[]
     */
    public function getBreadCrumb()
    {
        return $this->breadCrumb;
    }

    /**
     * @param NodeTranslation $parentNode The parent node
     * @param string          $slug       The slug
     *
     * @return NodeTranslation
     */
    public function getNodeBySlug(NodeTranslation $parentNode, $slug)
    {
        return $this->em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->getNodeTranslationForSlug($slug, $parentNode);
    }

    /**
     * @param string                                             $internalName The internal name
     * @param Node|NodeTranslation|NodeMenuItem|HasNodeInterface $parent       The parent
     *
     * @return NodeMenuItem|NULL
     */
    public function getNodeByInternalName($internalName, $parent = null)
    {
        $node = null;

        if (!is_null($parent)) {
            if ($parent instanceof NodeTranslation) {
                $parent = $parent->getNode();
            } else if ($parent instanceof NodeMenuItem) {
                $parent = $parent->getNode();
            } else if ($parent instanceof HasNodeInterface) {
                $parent = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($parent);
            }
            $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findOneBy(array('internalName' => $internalName, 'parent' => $parent->getId()));
            if (is_null($node)) {
                $nodes = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findBy(array('internalName' => $internalName));
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
            $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findOneBy(array('internalName' => $internalName));
        }
        if (!is_null($node)) {
            $nodeTranslation = $node->getNodeTranslation($this->lang, $this->includeoffline);
            if (!is_null($nodeTranslation)) {
                return $this->getNodemenuForNodeTranslation($nodeTranslation);
            }
        }

        return null;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @return NodeMenuItem|NULL
     */
    private function getNodemenuForNodeTranslation(NodeTranslation $nodeTranslation)
    {
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
            foreach ($nodeBreadCrumb as $nodeBreadCrumbItem) {
                $breadCrumbItemFromMain = $this->getBreadCrumbItemByNode($nodeBreadCrumbItem);
                if (!is_null($breadCrumbItemFromMain)) {
                    $parentNodeMenuItem = $breadCrumbItemFromMain;
                }
                $nodeTranslation = $nodeBreadCrumbItem->getNodeTranslation($this->lang, $this->includeoffline);
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
     * @param Node $node
     *
     * @return NodeMenuItem|NULL
     */
    private function getBreadCrumbItemByNode(Node $node)
    {
        foreach ($this->breadCrumb as $breadCrumbItem) {
            if ($breadCrumbItem->getNode()->getId() == $node->getId()) {
                return $breadCrumbItem;
            }
        }

        return null;
    }

    /**
     * @return boolean
     */
    public function isIncludeOffline()
    {
        return $this->includeoffline;
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function getAclHelper()
    {
        return $this->aclHelper;
    }

    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return boolean
     */
    public function isIncludeHiddenFromNav()
    {
        return $this->includehiddenfromnav;
    }
    
}