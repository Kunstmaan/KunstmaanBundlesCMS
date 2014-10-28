<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

/**
 * Implement this interface to define a custom Twig search view for your entity.
 */
interface SearchViewTemplateInterface
{
    /**
     * @return string
     */
    public function getSearchView();
}
