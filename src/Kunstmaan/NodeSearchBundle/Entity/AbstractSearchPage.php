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
        return array();
    }

    /*
     * return string
     */
    public function getDefaultView()
    {
        return 'KunstmaanNodeSearchBundle:AbstractSearchPage:view.html.twig';
    }

    /**
     * @return boolean
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
