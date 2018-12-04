<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role as BaseRole;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role Entity
 *
 * @ORM\Entity
 * @ORM\Table( name="kuma_roles" )
 * @UniqueEntity("role")
 */
class Role extends BaseRole
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="role", unique=true, length=70)
     */
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
     *
     * @return string
     */
    public function __toString()
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
     * @return RoleInterface
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
}
