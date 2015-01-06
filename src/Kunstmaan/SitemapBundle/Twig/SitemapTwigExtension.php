<?php

namespace Kunstmaan\SitemapBundle\Twig;

use Kunstmaan\NodeBundle\Helper\NodeMenuItem;

class SitemapTwigExtension extends \Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'hide_from_sitemap' => new \Twig_Function_Method($this, 'isHiddenFromSitemap'),
            'hide_children_from_sitemap' => new \Twig_Function_Method($this, 'isHiddenChildrenFromSitemap'),
        );
    }

    /**
     * Returns true when the item should be hidden from the sitemap
     *
     * @param NodeMenuItem $item
     *
     * @return \Kunstmaan\NodeBundle\Helper\NodeMenuItem
     */
    public function isHiddenFromSitemap(NodeMenuItem $item)
    {
        if (is_subclass_of($item->getNode()->getRefEntityName(), 'Kunstmaan\\SitemapBundle\\Helper\\HiddenFromSitemap')) {
            $page = $item->getPage();

            return $page->isHiddenFromSitemap();
        }

        return false;
    }

    /**
     * Returns true when the children of the item should be hidden from the sitemap
     *
     * @param NodeMenuItem $item
     *
     * @return bool
     */
    public function isHiddenChildrenFromSitemap(NodeMenuItem $item)
    {
        if (is_subclass_of($item->getNode()->getRefEntityName(), 'Kunstmaan\\SitemapBundle\\Helper\\HiddenFromSitemap')) {
            $page = $item->getPage();

            return $page->isChildrenHiddenFromSitemap();
        }

        return false;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "kunstmaan_sitemap_extension";
    }

}

