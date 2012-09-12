<?php

namespace Kunstmaan\AdminBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use FOS\UserBundle\Model\GroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_group")
 */
class Group implements RoleInterface, GroupInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_user_group_roles")
     */
    protected $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     */
    protected $rolescollection;

    /**
     * Constructor
     *
     * @param string $name Name of the group
     */
    public function __construct($name = '')
    {
        $this->name = $name;
        $this->roles = new ArrayCollection();
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
     * Get string representation of object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Returns an array of strings (needed because Symfony ACL doesn't support using RoleInterface yet)
     *
     * @return array
     */
    public function getRoles()
    {
        $result = array();
        foreach ($this->roles as $role) {
            $result[] = $role->getRole();
        }

        return $result;
    }

    /**
     * Returns the true ArrayCollection of Roles.
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getRolesCollection()
    {
        return $this->roles;
    }

    /**
     * Pass a string, get the desired Role object or null.
     *
     * @param string $role
     *
     * @return Role|null
     */
    public function getRole($role = null)
    {
        foreach ($this->roles as $roleItem) {
            if ($role == $roleItem->getRole()) {
                return $roleItem;
            }
        }

        return null;
    }

    /**
     * Pass a string, checks if we have that Role. Same functionality as getRole() except it returns a boolean.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if ($this->getRole($role)) {
            return true;
        }

        return false;
    }

    /**
     * Adds a Role object to the ArrayCollection. Can't type hint due to interface so throws Exception.
     *
     * @throws InvalidArgumentException
     *
     * @param Role $role
     */
    public function addRole($role)
    {
        if (!$role instanceof Role) {
            throw new \InvalidArgumentException("addRole takes a Role object as the parameter");
        }

        if (!$this->hasRole($role->getRole())) {
            $this->roles->add($role);
        }
    }

    /**
     * Pass a string, remove the Role object from collection.
     *
     * @param string $role
     */
    public function removeRole($role)
    {
        $roleElement = $this->getRole($role);
        if ($roleElement) {
            $this->roles->removeElement($roleElement);
        }
    }

    /**
     * Pass an ARRAY of Role objects and will clear the collection and re-set it with new Roles.
     *
     * @param Role[] $roles array of Role objects.
     */
    public function setRoles(array $roles)
    {
        $this->roles->clear();
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * Directly set the ArrayCollection of Roles. Type hinted as Collection which is the parent of (Array|Persistent)Collection.
     *
     * @param Doctrine\Common\Collections\Collection $collection
     */
    public function setRolesCollection(Collection $collection)
    {
        $this->roles = $collection;
    }

    /**
     * Return the name of the group
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the group
     *
     * @param string $name New name of the group
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
