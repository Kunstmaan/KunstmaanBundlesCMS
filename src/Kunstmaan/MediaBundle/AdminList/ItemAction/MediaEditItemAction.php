<?php

namespace Kunstmaan\MediaBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MediaEditItemAction implements ItemActionInterface
{
    /**
     * @return array
     */
    public function getUrlFor($item)
    {
        return [
            'path' => 'KunstmaanMediaBundle_media_show',
            'params' => ['mediaId' => $item->getId()],
        ];
    }

    /**
     * @return string
     */
    public function getLabelFor($item)
    {
        return 'Edit';
    }

    /**
     * @return string
     */
    public function getIconFor($item)
    {
        return 'edit';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return null;
    }
}
