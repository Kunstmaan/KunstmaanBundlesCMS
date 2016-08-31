<?php

namespace Kunstmaan\AdminListBundle\AdminList\ListAction;

/**
 * A list action can be used to configure actions which can be executed on the whole list.
 */
interface ListActionInterface
{
    /**
     * @return array
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @return string
     */
    public function getTemplate();
}
