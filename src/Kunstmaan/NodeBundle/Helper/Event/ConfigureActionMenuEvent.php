<?php

namespace Kunstmaan\AdminNodeBundle\Helper\Event;

use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\Event;
use Kunstmaan\AdminNodeBundle\Entity\NodeVersion;
use Knp\Menu\ItemInterface;

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
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     * @param \Kunstmaan\AdminNodeBundle\Entity\NodeVersion $activeNodeVersion
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
