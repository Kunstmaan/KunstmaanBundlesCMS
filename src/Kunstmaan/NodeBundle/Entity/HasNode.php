<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 14/11/11
 * Time: 16:29
 * To change this template use File | Settings | File Templates.
 */

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