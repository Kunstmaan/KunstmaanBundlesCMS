<?php

namespace Kunstmaan\AdminListBundle\AdminList\BulkAction;

/**
 * A bulk action can be used to configure actions which can be executed on a selection of list rows.
 */
interface BulkActionInterface
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
