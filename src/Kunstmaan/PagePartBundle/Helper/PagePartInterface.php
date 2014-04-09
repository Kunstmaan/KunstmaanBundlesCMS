<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\NodeBundle\Entity\PageInterface;
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
    public function getAdminView(PageInterface $page = null);

    /**
     * This method can be used to override the default view for a specific page type
     * @abstract
     * @return string
     */
    public function getOverrideView(PageInterface $page = null);

    /**
     * @return AbstractType
     */
    public function getDefaultAdminType();

}
