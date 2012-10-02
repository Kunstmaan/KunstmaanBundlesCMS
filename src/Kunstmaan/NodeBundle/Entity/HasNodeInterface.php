<?php

namespace Kunstmaan\NodeBundle\Entity;

use Kunstmaan\NodeBundle\Form\PageAdminType;

/**
 * HasNodeInterface Interface
 */
interface HasNodeInterface
{

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     *
     * @return HasNodeInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getPageTitle();

    /**
     * @return bool
     */
    public function isOnline();

    /**
     * @return HasNodeInterface
     */
    public function getParent();

    /**
     * @param HasNodeInterface $hasNode
     */
    public function setParent(HasNodeInterface $hasNode);

    /**
     * @todo: this should be moved to another location?
     *
     * @return PageAdminType
     */
    public function getDefaultAdminType();

}
