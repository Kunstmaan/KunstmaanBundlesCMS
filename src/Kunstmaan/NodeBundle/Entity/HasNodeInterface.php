<?php

namespace Kunstmaan\NodeBundle\Entity;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Symfony\Component\Form\AbstractType;

/**
 * HasNodeInterface Interface
 */
interface HasNodeInterface extends EntityInterface
{
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

    /**
     * When this is true there won't be any save, publish, copy, menu, meta, preview, etc.
     * It's basically not a page. Just a node where other pages can hang under.
     *
     * @return boolean
     */
    public function isStructureNode();

}
