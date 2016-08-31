<?php

namespace Kunstmaan\AdminListBundle\AdminList\ItemAction;

/**
 * An item action can be used to configure custom actions on a listed item.
 */
interface ItemActionInterface
{
    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getUrlFor($item);

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getLabelFor($item);

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item);

    /**
     * @return string
     */
    public function getTemplate();
}
