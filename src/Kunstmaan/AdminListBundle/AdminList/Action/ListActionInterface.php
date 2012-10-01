<?php

namespace Kunstmaan\AdminListBundle\AdminList\Action;

/**
 * ListActionInterface
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
