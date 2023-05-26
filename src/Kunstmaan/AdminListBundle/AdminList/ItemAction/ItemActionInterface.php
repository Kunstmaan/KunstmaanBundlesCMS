<?php

namespace Kunstmaan\AdminListBundle\AdminList\ItemAction;

/**
 * An item action can be used to configure custom actions on a listed item.
 */
interface ItemActionInterface
{
    /**
     * @return array
     */
    public function getUrlFor($item);

    /**
     * @return string
     */
    public function getLabelFor($item);

    /**
     * @return string
     */
    public function getIconFor($item);

    /**
     * @return string
     */
    public function getTemplate();
}
