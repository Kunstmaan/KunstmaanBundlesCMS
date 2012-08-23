<?php

namespace Kunstmaan\AdminNodeBundle\Menu;

use Knp\Menu\FactoryInterface;
use Kunstmaan\AdminNodeBundle\Helper\Event\ConfigureActionMenuEvent;
use Kunstmaan\AdminNodeBundle\Helper\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Router;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

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
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, EntityManager $em, Router $router, EventDispatcher $dispatcher)
    {
        $this->factory = $factory;
        $this->em      = $em;
        $this->router  = $router;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createSubActionsMenu(Request $request)
    {
        $activeNodeVersion = $this->getActiveNodeVersion();
        $menu              = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'sub_actions');

        if (!is_null($activeNodeVersion)) {
            $activeNodeTranslation = $activeNodeVersion->getNodeTranslation();
            if ('draft' != $activeNodeVersion->getType()) {
                if ($activeNodeTranslation->isOnline()) {
                    $menu->addChild('subaction.unpublish', array('linkAttributes' => array('data-toggle' => 'modal', 'data-target' => '#unpub')));
                }
                else {
                    $menu->addChild('subaction.unpublish', array('linkAttributes' => array('data-toggle' => 'modal', 'data-target' => '#pub')));
                }
            }
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
    public function createActionsMenu(Request $request)
    {
        $activeNodeVersion = $this->getActiveNodeVersion();
        $menu              = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'main_actions');

        if (!is_null($activeNodeVersion)) {
            $activeNodeTranslation = $activeNodeVersion->getNodeTranslation();

            if ('draft' == $activeNodeVersion->getType()) {
                $menu->addChild('action.saveasdraft', array('linkAttributes' => array('type' => 'submit', 'onClick' => 'isEdited=false', 'class' => 'btn btn-primary', 'value' => 'save', 'name' => 'save'), 'extras' => array('renderType' => 'button')));
                $menu->addChild('action.publish', array('linkAttributes' => array('type' => 'submit', 'class' => 'btn', 'value' => 'saveandpublish', 'name' => 'saveandpublish'), 'extras' => array('renderType' => 'button')));
                $menu->addChild('action.preview', array('uri' => $this->router->generate('_slug_draft', array('url' => $activeNodeTranslation->getUrl())), 'linkAttributes' => array('target' => '_blank', 'class' => 'btn')));
            }
            else {
                $menu->addChild('action.save', array('linkAttributes' => array('type' => 'submit', 'onClick' => 'isEdited=false', 'class' => 'btn btn-primary', 'value' => 'save', 'name' => 'save'), 'extras' => array('renderType' => 'button')));
                $menu->addChild('action.saveasdraft', array('linkAttributes' => array('type' => 'submit', 'class' => 'btn', 'value' => 'saveasdraft', 'name' => 'saveasdraft'), 'extras' => array('renderType' => 'button')));
                $menu->addChild('action.preview', array('uri' => $this->router->generate('_slug_preview', array('url' => $activeNodeTranslation->getUrl())), 'linkAttributes' => array('target' => '_blank', 'class' => 'btn')));

                $page               = $activeNodeVersion->getRef($this->em);
                $possibleChildPages = $page->getPossibleChildPageTypes();
                if (!empty($possibleChildPages)) {
                    $menu->addChild('action.addsubpage', array('linkAttributes' => array('type' => 'button', 'class' => 'btn', 'data-toggle' => 'modal', 'data-target' => '#add-subpage-modal'), 'extras' => array('renderType' => 'button')));
                }
            }
            $menu->addChild('action.delete', array('linkAttributes' => array('type' => 'button', 'class' => 'btn', 'onClick' => 'oldEdited = isEdited; isEdited=false', 'data-toggle' => 'modal', 'data-target' => '#delete-page-modal'), 'extras' => array('renderType' => 'button')));
        }

        $this->dispatcher->dispatch(Events::CONFIGURE_ACTION_MENU, new ConfigureActionMenuEvent($this->factory, $menu, $activeNodeVersion));

        return $menu;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createTopActionsMenu(Request $request)
    {
        $menu = $this->createActionsMenu($request);
        $menu->setChildrenAttribute('class', 'main_actions top');
        $menu->setChildrenAttribute('id', 'main_actions_top');

        return $menu;
    }

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     *
     * @return ActionMenuBuilder
     */
    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
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
