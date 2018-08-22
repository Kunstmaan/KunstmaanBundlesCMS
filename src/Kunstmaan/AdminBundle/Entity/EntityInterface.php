<?php

namespace Kunstmaan\AdminBundle\Entity;

interface EntityInterface 
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id The unique identifier
     *
     * @return EntityInterface
     */
    public function setId($id);
}
