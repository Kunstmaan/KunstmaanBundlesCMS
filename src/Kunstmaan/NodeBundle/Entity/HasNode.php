<?php

namespace Kunstmaan\AdminNodeBundle\Entity;

interface HasNode {

    public function getId();

    public function getTitle();

    public function isOnline();
    
    public function getParent();
    
    public function setParent(HasNode $hasNode);

    /**
     * Return an array containing all possible permissions for the page
     * @abstract
     * @return array
     */
    public function getPossiblePermissions();
}