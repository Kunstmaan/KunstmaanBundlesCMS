<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\AdminBundle\Entity\EntityInterface;

/**
 * PagePartInterface
 */
interface PagePartInterface extends EntityInterface
{
    /**
     * Returns the view used in the frontend
     *
     * @return string
     */
    public function getDefaultView();

    /**
     * Returns the view used in the backend
     *
     * @return string
     */
    public function getAdminView();

    /**
     * This method can be used to override the default view for a specific page type
     *
     * @param HasPagePartsInterface|null $page
     *
     * @return string
     */
    public function getView(HasPagePartsInterface $page = null);

    /**
     * Returns the default backend form type for the page part.
     *
     * @return string fully qualified class name of a form
     */
    public function getDefaultAdminType();
}
