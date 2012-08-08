<?php

namespace Kunstmaan\AdminNodeBundle\Entity;

/**
 * HasNodeInterface Interface
 */
interface HasNodeInterface
{

    /**
     * Get id
     *
     * @return integer
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
     * @return boolean
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