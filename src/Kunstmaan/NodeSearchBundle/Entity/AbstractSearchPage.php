<?php

namespace Kunstmaan\NodeSearchBundle\Entity;

use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface;
use Kunstmaan\NodeSearchBundle\ViewDataProvider\SearchPageViewDataProvider;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;

/**
 * AbstractSearchPage, extend this class to create your own SearchPage and extend the standard functionality
 */
class AbstractSearchPage extends AbstractPage implements IndexableInterface, SlugActionInterface, CustomViewDataProviderInterface
{
    public function __construct()
    {
        if (\get_class($this) === __CLASS__) {
            @trigger_error(sprintf('Instantiating the "%s" class is deprecated in KunstmaanNodeSearchBundle 5.9 and will be made abstract in KunstmaanNodeSearchBundle 6.0. Extend your implementation from this class instead.', __CLASS__), E_USER_DEPRECATED);
        }
    }

    /**
     * @deprecated since KunstmaanNodeSearchBundle 5.9 and will be removed in KunstmaanNodeSearchBundle 6.0. Use the `Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface` and a custom page render service instead.
     *
     * @return string
     */
    public function getControllerAction()
    {
        return 'KunstmaanNodeSearchBundle:AbstractSearchPage:service';
    }

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
