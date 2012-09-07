<?php

namespace Kunstmaan\AdminNodeBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminNodeBundle\Helper\Event\ConfigureActionMenuEvent;
use Kunstmaan\AdminNodeBundle\Helper\Event\Events;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Knp\Menu\FactoryInterface;

class ActionsMenuBuilder
{

    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $factory;

    /**
     * @var \Kunstmaan\AdminNodeBundle\Entity\NodeVersion
     */
    private $activeNodeVersion;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $context;

    /**
     * @param FactoryInterface         $factory
     * @param EntityManager            $em
     * @param RouterInterface          $router
     * @param EventDispatcherInterface $dispatcher
     * @param SecurityContextInterface $context
     */
    public function __construct(FactoryInterface $factory, EntityManager $em, RouterInterface $router, EventDispatcherInterface $dispatcher, SecurityContextInterface $context)
    {
        $this->factory = $factory;
        $this->em      = $em;
        $this->router  = $router;
        $this->dispatcher = $dispatcher;
        $this->context = $context;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createSubActionsMenu(Request $request = null)
    {
        $activeNodeVersion = $this->getActiveNodeVersion();
        $menu              = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'sub_actions');

        if (!is_null($activeNodeVersion)) {
            $menu->addChild('subaction.versions', array('linkAttributes' => array('data-toggle' => 'modal', 'data-target' => '#versions')));
        }

        $this->dispatcher->dispatch(Events::CONFIGURE_SUB_ACTION_MENU, new ConfigureActionMenuEvent($this->factory, $menu, $activeNodeVersion));

        return $menu;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createActionsMenu(Request $request = null)
    {
        $activeNodeVersion = $this->getActiveNodeVersion();
        $menu              = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'main_actions');

        if (!is_null($activeNodeVersion)) {
            $activeNodeTranslation = $activeNodeVersion->getNodeTranslation();
            $node = $activeNodeTranslation->getNode();

            if ('draft' == $activeNodeVersion->getType()) {
                if ($this->context->isGranted(PermissionMap::PERMISSION_EDIT, $node)) {
                    $menu->addChild('action.saveasdraft', array('linkAttributes' => array('type' => 'submit', 'onClick' => 'isEdited=false', 'class' => 'btn btn-primary', 'value' => 'save', 'name' => 'save'), 'extras' => array('renderType' => 'button')));
                }
                if ($this->context->isGranted(PermissionMap::PERMISSION_PUBLISH, $node)) {
                    $menu->addChild('action.publish', array('linkAttributes' => array('type' => 'submit', 'class' => 'btn', 'value' => 'saveandpublish', 'name' => 'saveandpublish'), 'extras' => array('renderType' => 'button')));
                }
                $menu->addChild('action.preview', array('uri' => $this->router->generate('_slug_draft', array('url' => $activeNodeTranslation->getUrl())), 'linkAttributes' => array('target' => '_blank', 'class' => 'btn')));
            } else {
                if ($this->context->isGranted(PermissionMap::PERMISSION_EDIT, $node) && $this->context->isGranted(PermissionMap::PERMISSION_PUBLISH, $node)) {
                    $menu->addChild('action.save', array('linkAttributes' => array('type' => 'submit', 'onClick' => 'isEdited=false', 'class' => 'btn btn-primary', 'value' => 'save', 'name' => 'save'), 'extras' => array('renderType' => 'button')));
                }
                if ($activeNodeTranslation->isOnline() &&  $this->context->isGranted(PermissionMap::PERMISSION_UNPUBLISH, $node)) {
                    $menu->addChild('action.unpublish', array('linkAttributes' => array('class' => 'btn', 'data-toggle' => 'modal', 'data-target' => '#unpub')));
                } elseif (!$activeNodeTranslation->isOnline() &&  $this->context->isGranted(PermissionMap::PERMISSION_PUBLISH, $node)) {
                    $menu->addChild('action.publish', array('linkAttributes' => array('class' => 'btn', 'data-toggle' => 'modal', 'data-target' => '#pub')));
                }
                if ($this->context->isGranted(PermissionMap::PERMISSION_EDIT, $node)) {
                    $menu->addChild('action.saveasdraft', array('linkAttributes' => array('type' => 'submit', 'class' => 'btn', 'value' => 'saveasdraft', 'name' => 'saveasdraft'), 'extras' => array('renderType' => 'button')));
                }
                $menu->addChild('action.preview', array('uri' => $this->router->generate('_slug_preview', array('url' => $activeNodeTranslation->getUrl())), 'linkAttributes' => array('target' => '_blank', 'class' => 'btn')));
            }
            $page = $activeNodeVersion->getRef($this->em);
            if (!is_null($page)) {
                $possibleChildPages = $page->getPossibleChildPageTypes();
                if (!empty($possibleChildPages)) {
                    $menu->addChild('action.addsubpage', array('linkAttributes' => array('type' => 'button', 'class' => 'btn', 'data-toggle' => 'modal', 'data-target' => '#add-subpage-modal'), 'extras' => array('renderType' => 'button')));
                }
            }
            if ($this->context->isGranted(PermissionMap::PERMISSION_DELETE, $node)) {
                $menu->addChild('action.delete', array('linkAttributes' => array('type' => 'button', 'class' => 'btn', 'onClick' => 'oldEdited = isEdited; isEdited=false', 'data-toggle' => 'modal', 'data-target' => '#delete-page-modal'), 'extras' => array('renderType' => 'button')));
            }
        }

        $this->dispatcher->dispatch(Events::CONFIGURE_ACTION_MENU, new ConfigureActionMenuEvent($this->factory, $menu, $activeNodeVersion));

        return $menu;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createTopActionsMenu(Request $request = null)
    {
        $menu = $this->createActionsMenu($request);
        $menu->setChildrenAttribute('class', 'main_actions top');
        $menu->setChildrenAttribute('id', 'main_actions_top');

        return $menu;
    }

    /**
     * Set activeNodeVersion
     *
     * @param \Kunstmaan\AdminNodeBundle\Entity\NodeVersion $activeNodeVersion
     */
    public function setActiveNodeVersion($activeNodeVersion)
    {
        $this->activeNodeVersion = $activeNodeVersion;

        return $this;
    }

    /**
     * Get activeNodeVersion
     *
     * @return \Kunstmaan\AdminNodeBundle\Entity\NodeVersion
     */
    public function getActiveNodeVersion()
    {
        return $this->activeNodeVersion;
    }

}
