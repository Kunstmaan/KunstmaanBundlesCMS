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
     * @return HasNodeInterface
     */
    public function getParent();

    /**
     * @param HasNodeInterface $hasNode
     */
    public function setParent(HasNodeInterface $hasNode);

    /**
     * @return PageAdminType
     */
    public function getDefaultAdminType();

    /**
     * @return array
     */
    public function getPossibleChildTypes();

}
