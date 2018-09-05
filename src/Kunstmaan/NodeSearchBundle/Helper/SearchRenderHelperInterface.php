<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Interface SearchRenderHelperInterface.
 *
 * Implement this interface to define custom rendering of the search.
 */
interface SearchRenderHelperInterface
{
    /**
     * Render default search view (all indexable pageparts in the main context
     * of the page)
     *
     * @param NodeTranslation       $nodeTranslation
     * @param HasPagePartsInterface $page
     * @param EngineInterface       $renderer
     *
     * @return string
     */
    public function renderDefaultSearchView(NodeTranslation $nodeTranslation, HasPagePartsInterface $page, EngineInterface $renderer);

    /**
     * Render a custom search view
     *
     * @param NodeTranslation             $nodeTranslation
     * @param SearchViewTemplateInterface $page
     * @param EngineInterface             $renderer
     *
     * @return string
     */
    public function renderCustomSearchView(NodeTranslation $nodeTranslation, SearchViewTemplateInterface $page, EngineInterface $renderer);
}
