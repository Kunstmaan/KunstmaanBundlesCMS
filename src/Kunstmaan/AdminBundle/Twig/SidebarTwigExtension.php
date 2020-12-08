<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class SidebarTwigExtension extends AbstractExtension
{
    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('hideSidebarInNodeEditAdmin', [$this, 'hideSidebarInNodeEditAdmin']),
        ];
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
