<?php

namespace Kunstmaan\NodeBundle\Entity;

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

}
