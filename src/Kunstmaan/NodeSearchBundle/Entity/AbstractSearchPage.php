<?php

namespace Kunstmaan\NodeSearchBundle\Entity;

use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;

/**
 * AbstractSearchPage, extend this class to create your own SearchPage and extend the standard functionality
 */
class AbstractSearchPage extends AbstractPage implements IndexableInterface, SlugActionInterface
{
    public function __construct()
    {
        if (\get_class($this) === __CLASS__) {
            @trigger_error(sprintf('Instantiating the "%s" class is deprecated in KunstmaanNodeSearchBundle 5.9 and will be made abstract in KunstmaanNodeSearchBundle 6.0. Extend your implementation from this class instead.', __CLASS__), E_USER_DEPRECATED);
        }
    }

    /**
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
}
