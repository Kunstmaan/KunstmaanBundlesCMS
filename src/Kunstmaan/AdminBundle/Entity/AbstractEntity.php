<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The Abstract ORM entity
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'bigint')]
    #[ORM\GeneratedValue('AUTO')]
    protected $id;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id The unique identifier
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Return string representation of entity
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }
}
