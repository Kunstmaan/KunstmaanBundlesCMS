<?php

namespace Kunstmaan\SitemapBundle\Twig;

use Kunstmaan\NodeBundle\Helper\NodeMenuItem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SitemapTwigExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hide_from_sitemap', [$this, 'isHiddenFromSitemap']),
            new TwigFunction('hide_children_from_sitemap', [$this, 'isHiddenChildrenFromSitemap']),
        ];
    }

    /**
     * Returns true when the item should be hidden from the sitemap
     */
    public function isHiddenFromSitemap(NodeMenuItem $item): bool
    {
        if (is_subclass_of($item->getNode()->getRefEntityName(), 'Kunstmaan\\SitemapBundle\\Helper\\HiddenFromSitemapInterface')) {
            $page = $item->getPage();

            return $page->isHiddenFromSitemap();
        }

        return false;
    }

    /**
     * Returns true when the children of the item should be hidden from the sitemap
     */
    public function isHiddenChildrenFromSitemap(NodeMenuItem $item): bool
    {
        if (is_subclass_of($item->getNode()->getRefEntityName(), 'Kunstmaan\\SitemapBundle\\Helper\\HiddenFromSitemapInterface')) {
            $page = $item->getPage();

            return $page->isChildrenHiddenFromSitemap();
        }

        return false;
    }
}
