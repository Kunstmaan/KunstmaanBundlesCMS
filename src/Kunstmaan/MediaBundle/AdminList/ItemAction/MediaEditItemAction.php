<?php

namespace Kunstmaan\MediaBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MediaEditItemAction implements ItemActionInterface
{
    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getUrlFor($item)
    {
        return array(
          'path' => 'KunstmaanMediaBundle_media_show',
          'params' => array('mediaId' => $item->getId()),
        );
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getLabelFor($item)
    {
        return 'Edit';
    }

    /**
     * @param mixed $item
     *
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
