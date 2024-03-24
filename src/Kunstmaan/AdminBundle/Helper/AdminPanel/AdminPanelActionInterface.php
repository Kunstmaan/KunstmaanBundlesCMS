<?php

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

interface AdminPanelActionInterface
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
     * @return string|null
     */
    public function getIcon();

    /**
     * @return string|null
     */
    public function getTemplate();
}
