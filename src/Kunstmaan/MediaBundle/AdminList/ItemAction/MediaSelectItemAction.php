<?php

namespace Kunstmaan\MediaBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MediaSelectItemAction implements ItemActionInterface
{
    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getUrlFor($item)
    {
        return null;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getLabelFor($item)
    {
        return 'Select';
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return null;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@KunstmaanMedia/AdminList/ItemAction/select.html.twig';
    }
}
