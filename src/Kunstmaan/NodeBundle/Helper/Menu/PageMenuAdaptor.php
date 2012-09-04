<?php

namespace Kunstmaan\AdminNodeBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Helper\NodeMenu;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface as KnpMenu;

/**
 * The Page Menu Adaptor
 */
class PageMenuAdaptor implements MenuAdaptorInterface
{
    private $em;
    private $securityContext;
    private $aclHelper;
    private $nodeMenu;

    /**
     * @param EntityManager            $em
     * @param SecurityContextInterface $securityContext
     * @param AclHelper                $aclHelper
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclHelper $aclHelper)
    {
        $this->em              = $em;
        $this->securityContext = $securityContext;
        $this->aclHelper       = $aclHelper;
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu      The MenuBuilder
     * @param MenuItem[]  &$children The current children
     * @param MenuItem    $parent    The parent Menu item
     * @param Request     $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (is_null($this->nodeMenu)) {
            /* @var Node $node */
            $node = null;
            if ($request->attributes->get('_route') == 'KunstmaanAdminNodeBundle_pages_edit') {
                $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findOneById($request->attributes->get('id'));
            }
            $this->nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $request->getSession()->getLocale(), $node, 'EDIT', true, true);
        }
        if (is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('KunstmaanAdminNodeBundle_pages');
            $menuItem->setInternalname("Pages");
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        } else {
            if ('KunstmaanAdminNodeBundle_pages' == $parent->getRoute()) {
                $topNodes = $this->nodeMenu->getTopNodes();
                $currentId = $request->attributes->get('id');
                $this->processNodes($currentId, $menu, $children, $topNodes, $parent, $request);
            } else if ('KunstmaanAdminNodeBundle_pages_edit' == $parent->getRoute()) {
                $parentRouteParams = $parent->getRouteparams();
                /* @var Node $node */
                $node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findOneById($parentRouteParams['id']);
                $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $request->getSession()->getLocale(), $node, 'EDIT', true, true);
                $childNodes = $nodeMenu->getCurrent()->getChildren();
                $currentId = $request->attributes->get('id');
                $this->processNodes($currentId, $menu, $children, $childNodes, $parent, $request);
            }
        }
    }

    /**
     * @param integer $currentId
     * @param MenuBuilder $menu
     * @param MenuItem[] $children
     * @param NodeMenuItem[] $nodes
     * @param MenuItem $parent
     * @param Request $request
     */
    public function processNodes($currentId, MenuBuilder $menu, array &$children, array $nodes, MenuItem $parent = null, Request $request = null)
    {
        if (isset($currentId)) {
            /* @var Node $currentNode */
            $currentNode = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findOneById($currentId);
            if (!is_null($currentNode)) {
                $parentNodes = $currentNode->getParents();
            } else {
                $parentNodes = array();
            }
        }

        foreach ($nodes as $child) {
            $menuItem = new MenuItem($menu);
            $menuItem->setRoute('KunstmaanAdminNodeBundle_pages_edit');
            $menuItem->setRouteparams(array('id' => $child->getId()));
            $menuItem->setInternalname($child->getTitle());
            $menuItem->setParent($parent);
            $menuItem->setRole('page');
            $menuItem->setWeight($child->getNodeTranslation()->getWeight());

            if (isset($currentNode) && stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                if ($currentNode->getId() == $child->getId()) {
                    $menuItem->setActive(true);
                } else {
                    foreach ($parentNodes as $parentNode) {
                        if ($parentNode->getId() == $child->getId()) {
                            $menuItem->setActive(true);
                            break;
                        }
                    }
                }
            }
            $children[] = $menuItem;
        }
    }
}