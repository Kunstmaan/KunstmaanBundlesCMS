<?php

namespace Kunstmaan\AdminNodeBundle\Event;

use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\Event;
use Kunstmaan\AdminNodeBundle\Entity\NodeVersion;
use Knp\Menu\ItemInterface;

/**
 * ConfigureActionMenuEvent
 */
class ConfigureActionMenuEvent extends Event
{

    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $factory;

    /**
     * @var \Knp\Menu\ItemInterface
     */
    private $menu;

    /**
     * @var \Kunstmaan\AdminNodeBundle\Entity\NodeVersion
     */
    private $activeNodeVersion;

    /**
     * @param FactoryInterface $factory           The factory
     * @param ItemInterface    $menu              The menu
     * @param NodeVersion      $activeNodeVersion The nodeversion
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, NodeVersion $activeNodeVersion)
    {
        $this->factory = $factory;
        $this->menu    = $menu;
        $this->activeNodeVersion = $activeNodeVersion;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
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
