<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table( name="kuma_roles" )
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_roles')]
#[UniqueEntity('role')]
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue('AUTO')]
    protected $id;

    /**
     * @ORM\Column(type="string", name="role", unique=true, length=70)
     */
    #[ORM\Column(name: 'role', type: 'string', unique: true, length: 70)]
    #[Assert\NotBlank]
    protected $role;

    /**
     * Populate the role field
     *
     * @param string $role
     */
    public function __construct($role)
    {
        $this->role = $role;
    }

    /**
     * Return the role field.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Return the string representation of the role entity.
     */
    public function __toString(): string
    {
        return (string) $this->role;
    }

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
     * Modify the role field.
     *
     * @param string $role ROLE_FOO etc
     *
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
}
