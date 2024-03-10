<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SidebarTwigExtension extends AbstractExtension
{
    private bool $hideSidebar;

    public function __construct(bool $hideSidebar)
    {
        $this->hideSidebar = $hideSidebar;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hideSidebarInNodeEditAdmin', [$this, 'hideSidebarInNodeEditAdmin']),
        ];
    }

    public function hideSidebarInNodeEditAdmin($node): bool
    {
        return $this->hideSidebar || $node instanceof HideSidebarInNodeEditInterface;
    }
}
