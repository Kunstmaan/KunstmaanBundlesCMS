<?php

namespace Kunstmaan\NodeSearchBundle\Entity;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface;
use Kunstmaan\NodeSearchBundle\ViewDataProvider\SearchPageViewDataProvider;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;

/**
 * AbstractSearchPage, extend this class to create your own SearchPage and extend the standard functionality
 */
abstract class AbstractSearchPage extends AbstractPage implements IndexableInterface, CustomViewDataProviderInterface
{
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [];
    }

    /**
     * return string
     */
    public function getDefaultView()
    {
        return '@KunstmaanNodeSearch/AbstractSearchPage/view.html.twig';
    }

    /**
     * @return bool
     */
    public function isIndexable()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getSearcher()
    {
        return 'kunstmaan_node_search.search.node';
    }

    public function getViewDataProviderServiceId(): string
    {
        return SearchPageViewDataProvider::class;
    }
}
