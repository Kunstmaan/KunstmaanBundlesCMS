<?php

namespace Tests\Kunstmaan\AdminBundle\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * TestEntity
 */
class TestEntity extends AbstractEntity
{
    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

}
