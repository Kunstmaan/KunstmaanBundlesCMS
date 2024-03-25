<?php

namespace Kunstmaan\NodeBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Symfony\Contracts\EventDispatcher\Event;

final class ConfigureActionMenuEvent extends Event
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ItemInterface
     */
    private $menu;

    /**
     * @var NodeVersion
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
        $this->menu = $menu;
        $this->activeNodeVersion = $activeNodeVersion;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    /**
     * Get activeNodeVersion
     */
    public function getActiveNodeVersion(): NodeVersion
    {
        return $this->activeNodeVersion;
    }
}
