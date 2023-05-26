<?php

namespace Kunstmaan\MediaBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MediaSelectItemAction implements ItemActionInterface
{
    /**
     * @return array
     */
    public function getUrlFor($item)
    {
        return null;
    }

    /**
     * @return string
     */
    public function getLabelFor($item)
    {
        return 'Select';
    }

    /**
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
