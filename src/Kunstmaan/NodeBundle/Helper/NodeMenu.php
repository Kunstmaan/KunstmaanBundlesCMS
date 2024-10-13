<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NodeMenu
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AclHelper
     */
    private $aclHelper;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var Node
     */
    private $currentNode;

    /**
     * @var string
     */
    private $permission = PermissionMap::PERMISSION_VIEW;

    /**
     * @var bool
     */
    private $includeOffline = false;

    /**
     * @var bool
     */
    private $includeHiddenFromNav = false;

    /**
     * @var NodeMenuItem[]
     */
    private $topNodeMenuItems;

    /**
     * @var NodeMenuItem[]
     */
    private $breadCrumb;

    /**
     * @var NodeMenuItem
     */
    private $rootNodeMenuItem;

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @param EntityManagerInterface       $em                  The entity manager
     * @param TokenStorageInterface        $tokenStorage        The security token storage
     * @param AclHelper                    $aclHelper           The ACL helper pages
     * @param DomainConfigurationInterface $domainConfiguration The current domain configuration
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AclHelper $aclHelper,
        DomainConfigurationInterface $domainConfiguration,
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->aclHelper = $aclHelper;
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function setCurrentNode(?Node $currentNode = null)
    {
        $this->currentNode = $currentNode;
    }

    /**
     * @param string $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    /**
     * @param bool $includeOffline
     */
    public function setIncludeOffline($includeOffline)
    {
        $this->includeOffline = $includeOffline;
    }

    /**
     * @param bool $includeHiddenFromNav
     */
    public function setIncludeHiddenFromNav($includeHiddenFromNav)
    {
        $this->includeHiddenFromNav = $includeHiddenFromNav;
    }

    /**
     * @return NodeMenuItem[]
     */
    public function getTopNodes()
    {
        $rootNode = $this->domainConfiguration->getRootNode();
        if (null === $rootNode) {
            $rootNodes = $this->em->getRepository(Node::class)->getRootNodes();
            $rootNode = $rootNodes[0];
        }

        return [
            new NodeMenuItem($rootNode, $rootNode->getNodeTranslation($this->locale), null, $this),
        ];
    }

    /**
     * @return NodeMenuItem[]
     */
    public function getBreadCrumb()
    {
        if (\is_array($this->breadCrumb)) {
            return $this->breadCrumb;
        }

        $this->breadCrumb = [];

        /* @var NodeRepository $repo */
        $repo = $this->em->getRepository(Node::class);

        // Generate breadcrumb MenuItems - fetch *all* languages so you can link translations if needed
        $parentNodes = $repo->getAllParents($this->currentNode);
        $parentNodeMenuItem = null;
        /* @var Node $parentNode */
        foreach ($parentNodes as $parentNode) {
            $nodeTranslation = $parentNode->getNodeTranslation(
                $this->locale,
                $this->includeOffline
            );
            if (!\is_null($nodeTranslation)) {
                $nodeMenuItem = new NodeMenuItem(
                    $parentNode,
                    $nodeTranslation,
                    $parentNodeMenuItem,
                    $this
                );
                $this->breadCrumb[] = $nodeMenuItem;
                $parentNodeMenuItem = $nodeMenuItem;
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
        if (\count($breadCrumb) > 0) {
            return $breadCrumb[\count($breadCrumb) - 1];
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
        if (\count($breadCrumb) >= $depth) {
            return $breadCrumb[$depth - 1];
        }

        return null;
    }

    /**
     * @param bool $includeHiddenFromNav
     *
     * @return NodeMenuItem[]
     */
    public function getChildren(Node $node, $includeHiddenFromNav = true)
    {
        $childNodes = $this->em->getRepository(Node::class)->getChildNodes(
            $node->getId(),
            $this->locale,
            $this->permission,
            $this->aclHelper,
            $includeHiddenFromNav
        );

        $children = [];
        foreach ($childNodes as $childNode) {
            $nt = $childNode->getNodeTranslation($this->locale);
            if (null === $nt) {
                continue;
            }

            $children[] = new NodeMenuItem($childNode, $nt, false, $this);
        }

        return $children;
    }

    /**
     * @param bool $includeHiddenFromNav
     *
     * @return NodeMenuItem[]
     */
    public function getSiblings(Node $node, $includeHiddenFromNav = true)
    {
        $parent = $this->getParent($node);
        if (false === $parent) {
            return [];
        }

        return array_filter(
            $this->getChildren($parent, $includeHiddenFromNav),
            static function (NodeMenuItem $item) use ($node) {
                return $item->getNode() !== $node;
            }
        );
    }

    /**
     * @param bool $includeHiddenFromNav
     *
     * @return NodeMenuItem|false
     */
    public function getPreviousSibling(Node $node, $includeHiddenFromNav = true)
    {
        if (false !== $parent = $this->getParent($node)) {
            $siblings = $this->getChildren($parent, $includeHiddenFromNav);

            foreach ($siblings as $index => $child) {
                if ($child->getNode() === $node && ($index - 1 >= 0)) {
                    return $siblings[$index - 1];
                }
            }
        }

        return false;
    }

    /**
     * @param bool $includeHiddenFromNav
     *
     * @return NodeMenuItem|false
     */
    public function getNextSibling(Node $node, $includeHiddenFromNav = true)
    {
        if (false !== $parent = $this->getParent($node)) {
            $siblings = $this->getChildren($parent, $includeHiddenFromNav);

            $siblingCount = \count($siblings);
            foreach ($siblings as $index => $child) {
                if ($child->getNode() === $node && (($index + 1) < $siblingCount)) {
                    return $siblings[$index + 1];
                }
            }
        }

        return false;
    }

    /**
     * @return Node|false
     */
    public function getParent(Node $node)
    {
        return $node->getParent() ?? false;
    }

    /**
     * @param NodeTranslation $parentNode The parent node
     * @param string          $slug       The slug
     *
     * @return NodeTranslation
     */
    public function getNodeBySlug(NodeTranslation $parentNode, $slug)
    {
        return $this->em->getRepository(NodeTranslation::class)->getNodeTranslationForSlug($slug, $parentNode);
    }

    /**
     * @param string                                        $internalName
     * @param NodeTranslation|NodeMenuItem|HasNodeInterface $parent
     * @param bool                                          $includeOffline
     *
     * @return NodeMenuItem|null
     */
    public function getNodeByInternalName($internalName, $parent = null, $includeOffline = null)
    {
        trigger_deprecation('kunstmaan/node-bundle', '7.2', 'The "%s" method is deprecated and will be removed in 8.0. Use the "%s" repository method or the "get_node_by_internal_name" twig method instead.', __METHOD__, '\Kunstmaan\NodeBundle\Repository\NodeRepository::getNodesByInternalName');

        $repo = $this->em->getRepository(Node::class);
        $includeOffline = $includeOffline ?? $this->includeOffline;

        $parentId = false;
        if ($parent instanceof NodeTranslation) {
            $parentId = $parent->getNode()->getId();
        } elseif ($parent instanceof NodeMenuItem) {
            $parentId = $parent->getNode()->getId();
        } elseif ($parent instanceof HasNodeInterface) {
            $parentId = $repo->getNodeFor($parent)->getId();
        }

        $nodes = $repo->getNodesByInternalName($internalName, $this->locale, $parentId, $includeOffline);
        if (count($nodes) === 0) {
            return null;
        }

        $returnNode = $nodes[0];

        return new NodeMenuItem($returnNode, $returnNode->getNodeTranslation($this->locale), false, $this);
    }

    /**
     * Returns the current root node menu item
     */
    public function getRootNodeMenuItem()
    {
        if (\is_null($this->rootNodeMenuItem)) {
            $rootNode = $this->domainConfiguration->getRootNode();
            if (!\is_null($rootNode)) {
                $nodeTranslation = $rootNode->getNodeTranslation(
                    $this->locale,
                    $this->includeOffline
                );
                $this->rootNodeMenuItem = new NodeMenuItem(
                    $rootNode,
                    $nodeTranslation,
                    false,
                    $this
                );
            } else {
                $this->rootNodeMenuItem = $this->breadCrumb[0];
            }
        }

        return $this->rootNodeMenuItem;
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
     * @deprecated since 7.2. Use the tokenStorage service or twig global app.user variable
     *
     * @return BaseUser
     */
    public function getUser()
    {
        trigger_deprecation('kunstmaan/node-bundle', '7.2', 'The "%s" method is deprecated and will be removed in 8.0. Use the "security.token_storage" or retreive the user with the "app.user" global variable.', __METHOD__);

        return $this->tokenStorage->getToken()->getUser();
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return TokenStorageInterface
     */
    public function getTokenStorage()
    {
        trigger_deprecation('kunstmaan/node-bundle', '7.2', 'The "%s" method is deprecated and will be removed in 8.0.', __METHOD__);

        return $this->tokenStorage;
    }

    /**
     * @return AclHelper
     */
    public function getAclHelper()
    {
        trigger_deprecation('kunstmaan/node-bundle', '7.2', 'The "%s" method is deprecated and will be removed in 8.0.', __METHOD__);

        return $this->aclHelper;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return bool
     */
    public function isIncludeHiddenFromNav()
    {
        return $this->includeHiddenFromNav;
    }

    /**
     * Check if provided slug is in active path
     *
     * @param string $slug
     *
     * @return bool
     */
    public function getActive($slug)
    {
        $bc = $this->getBreadCrumb();
        foreach ($bc as $bcItem) {
            if ($bcItem->getSlug() == $slug) {
                return true;
            }
        }

        return false;
    }

    /**
     * @deprecated since 7.2.
     * @return bool
     */
    public function isInitialized()
    {
        trigger_deprecation('kunstmaan/node-bundle', '7.2', 'The "%s" method is deprecated and will be removed in 8.0.', __METHOD__);

        return true;
    }
}
