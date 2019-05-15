<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SidebarTwigExtension extends AbstractExtension
{
    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('hideSidebarInNodeEditAdmin', array($this, 'hideSidebarInNodeEditAdmin')),
        );
    }

    /**
     * Return the admin menu MenuBuilder.
     *
     * @return MenuBuilder
     */
    public function hideSidebarInNodeEditAdmin($node)
    {
        return $node instanceof HideSidebarInNodeEditInterface;
    }
}
