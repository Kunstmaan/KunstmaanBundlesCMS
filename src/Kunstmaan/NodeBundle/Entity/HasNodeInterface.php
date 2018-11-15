<?php

namespace Kunstmaan\NodeBundle\Entity;

use Kunstmaan\AdminBundle\Entity\EntityInterface;

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
     * Get FQCN of form type.
     *
     * @return string
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
     * @return bool
     */
    public function isStructureNode();
}
