<?php

namespace Kunstmaan\MediaBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class MediaDeleteItemAction implements ItemActionInterface
{
    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @param string $redirectUrl
     */
    public function __construct($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getUrlFor($item)
    {
        return array(
          'path' => 'KunstmaanMediaBundle_media_delete',
          'params' => array('mediaId' => $item->getId(), 'redirectUrl' => $this->redirectUrl),
        );
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getLabelFor($item)
    {
        return 'Delete';
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return 'remove-sign';
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return null;
    }
}
