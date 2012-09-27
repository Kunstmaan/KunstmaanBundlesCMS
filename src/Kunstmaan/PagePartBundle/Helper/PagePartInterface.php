<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Symfony\Component\Form\AbstractType;

/**
 * PagePartInterface
 */
interface PagePartInterface
{

    /**
     * Returns the view used in the frontend
     * @abstract
     * @return string
     */
    public function getDefaultView();

    /**
     * Returns the view used in the backend
     * @abstract
     * @return string
     */
    public function getAdminView();

    /**
     * @return AbstractType
     */
    public function getDefaultAdminType();

}
