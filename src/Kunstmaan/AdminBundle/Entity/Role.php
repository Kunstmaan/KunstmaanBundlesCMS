<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Role Entity
 *
 * @ORM\Entity
 * @ORM\Table( name="kuma_roles" )
 * @UniqueEntity("role")
 */
class Role implements RoleInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="role", unique=true, length=70)
     * @NotBlank()
     */
    protected $role;

    /**
     * Populate the role field
     *
     * @param string $role ROLE_FOO etc
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
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
    
}
