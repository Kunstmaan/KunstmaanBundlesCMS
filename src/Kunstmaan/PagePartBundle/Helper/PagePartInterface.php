<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Symfony\Component\Form\AbstractType;

/**
 * PagePartInterface
 */
interface PagePartInterface extends EntityInterface
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
     * This method can be used to override the default view for a specific page type
     * @abstract
     * @return string
     */
    public function getView(HasPagePartsInterface $page = null);

    /**
     * @return AbstractType
     */
    public function getDefaultAdminType();
}
