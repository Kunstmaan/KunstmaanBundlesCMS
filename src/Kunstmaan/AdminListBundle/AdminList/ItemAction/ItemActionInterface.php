<?php

namespace Kunstmaan\AdminListBundle\AdminList\Action;

/**
 * An item action can be used to configure custom actions on a listed item.
 */
interface ItemActionInterface
{

    /**
     * @param $item
     *
     * @return array
     */
    public function getUrlFor($item);

    /**
     * @param $item
     *
     * @return string
     */
    public function getLabelFor($item);

    /**
     * @param $item
     *
     * @return string
     */
    public function getIconFor($item);

    /**
     * @return string
     */
    public function getTemplate();

}
