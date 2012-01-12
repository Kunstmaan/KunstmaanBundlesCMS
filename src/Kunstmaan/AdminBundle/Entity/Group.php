<?php

namespace Kunstmaan\AdminBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use FOS\UserBundle\Model\GroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\GroupRepository")
 * @ORM\Table(name="user_group")
 */
class Group implements RoleInterface, GroupInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
    * @ORM\OneToMany(targetEntity="Permission", mappedBy="refGroup")
    */
    protected $permissions;

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


    public function getPermissions() {
        return $this->permissions;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString() {
        return $this->getName();
    }

    /**
     * Returns an ARRAY of Role objects with the default Role object appended.
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Returns the true ArrayCollection of Roles.
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getRolesCollection() {
        return $this->roles;
    }

    /**
     * Pass a string, get the desired Role object or null.
     * @param string $role
     * @return Role|null
     */
    public function getRole($role = null) {
        foreach ($this->getRoles() as $roleItem) {
            if ($role == $roleItem->getRole()) {
                return $roleItem;
            }
        }
        return null;
    }

    /**
     * Pass a string, checks if we have that Role. Same functionality as getRole() except returns a real boolean.
     * @param string $role
     * @return boolean
     */
    public function hasRole($role) {
        if ($this->getRole($role)){
            return true;
        }
        return false;
    }

    /**
     * Adds a Role OBJECT to the ArrayCollection. Can't type hint due to interface so throws Exception.
     * @throws Exception
     * @param Role $role
     */
    public function addRole($role) {
        if (!$role instanceof Role) {
            throw new \Exception( "addRole takes a Role object as the parameter" );
        }

        if (!$this->hasRole($role->getRole())) {
            $this->roles->add($role);
        }
    }

    /**
     * Pass a string, remove the Role object from collection.
     * @param string $role
     */
    public function removeRole($role) {
        $roleElement = $this->getRole($role);
        if ($roleElement) {
            $this->roles->removeElement($roleElement);
        }
    }

    /**
     * Pass an ARRAY of Role objects and will clear the collection and re-set it with new Roles.
     * Type hinted array due to interface.
     * @param array $roles Of Role objects.
     */
    public function setRoles(array $roles) {
        $this->roles->clear();
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * Directly set the ArrayCollection of Roles. Type hinted as Collection which is the parent of (Array|Persistent)Collection.
     * @param Doctrine\Common\Collections\Collection $role
     */
    public function setRolesCollection(Collection $collection) {
        $this->roles = $collection;
    }


    public function __construct($name = '', $roles = array())
    {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }


    public function setName($name)
    {
        $this->name = $name;
    }
}