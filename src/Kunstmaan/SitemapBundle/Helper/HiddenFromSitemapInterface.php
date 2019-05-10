<?php

namespace Kunstmaan\SitemapBundle\Helper;

/**
 * Implement this interface to give you control over the Sitemap behavior of this page
 */
interface HiddenFromSitemapInterface
{
    /**
     * Returns true when the page is to be hidden from the generated sitemap
     *
     * @return bool
     */
    public function isHiddenFromSitemap();

    /**
     * Returns true when the page's children should be hidden from the generated sitemap
     *
     * @return bool
     */
    public function isChildrenHiddenFromSitemap();
}
