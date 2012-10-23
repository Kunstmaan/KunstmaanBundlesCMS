<?php

namespace Kunstmaan\NodeBundle\Entity;

use Symfony\Component\Form\AbstractType;

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
     * @return AbstractType
     */
    public function getDefaultAdminType();

    /**
     * @return array
     */
    public function getPossibleChildTypes();

}
