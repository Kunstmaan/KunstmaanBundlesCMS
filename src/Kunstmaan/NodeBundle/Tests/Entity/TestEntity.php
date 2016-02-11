<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

use Symfony\Component\Form\AbstractType;

/**
 * TestEntity
 */
class TestEntity extends AbstractEntity implements HasNodeInterface
{
    /**
     * @param int $id
     */
    public function __construct($id = 0)
    {
        $this->setId($id);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return AbstractPage
     */
    public function setTitle($title)
    {
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
    }

    /**
     * @return bool
     */
    public function isOnline()
    {
    }

    /**
     * @return HasNodeInterface
     */
    public function getParent()
    {
    }

    /**
     * @param HasNodeInterface $hasNode
     */
    public function setParent(HasNodeInterface $hasNode)
    {
    }

    /**
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
    }

    /**
     * @return boolean
     */
    public function isStructureNode()
    {
        return false;
    }
}
