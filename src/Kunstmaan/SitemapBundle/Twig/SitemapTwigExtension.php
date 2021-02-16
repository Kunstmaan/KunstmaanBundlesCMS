<?php

namespace Kunstmaan\SitemapBundle\Twig;

use Kunstmaan\NodeBundle\Helper\NodeMenuItem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class SitemapTwigExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('hide_from_sitemap', [$this, 'isHiddenFromSitemap']),
            new TwigFunction('hide_children_from_sitemap', [$this, 'isHiddenChildrenFromSitemap']),
        ];
    }

    /**
     * Returns true when the item should be hidden from the sitemap
     *
     * @return \Kunstmaan\NodeBundle\Helper\NodeMenuItem
     */
    public function isHiddenFromSitemap(NodeMenuItem $item)
    {
        if (is_subclass_of($item->getNode()->getRefEntityName(), 'Kunstmaan\\SitemapBundle\\Helper\\HiddenFromSitemapInterface')) {
            $page = $item->getPage();

            return $page->isHiddenFromSitemap();
        }

        return false;
    }

    /**
     * Returns true when the children of the item should be hidden from the sitemap
     *
     * @return bool
     */
    public function isHiddenChildrenFromSitemap(NodeMenuItem $item)
    {
        if (is_subclass_of($item->getNode()->getRefEntityName(), 'Kunstmaan\\SitemapBundle\\Helper\\HiddenFromSitemapInterface')) {
            $page = $item->getPage();

            return $page->isChildrenHiddenFromSitemap();
        }

        return false;
    }
}
