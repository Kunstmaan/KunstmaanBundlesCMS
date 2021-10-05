<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final
 *
 * @internal
 */
trait RolePropertiesTrait
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
