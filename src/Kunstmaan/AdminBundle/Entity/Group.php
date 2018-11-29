<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Group
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_groups")
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
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="kuma_groups_roles",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * Construct a new group
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
        /* @var $role RoleInterface */
        foreach ($this->roles as $role) {
            $result[] = $role->getRole();
        }

        return $result;
    }

    /**
     * Returns the true ArrayCollection of Roles.
     *
     * @return ArrayCollection
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
        /* @var $roleItem RoleInterface */
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
     * @param Role $role
     *
     * @return GroupInterface
     *
     * @throws InvalidArgumentException
     */
    public function addRole($role)
    {
        if (!$role instanceof Role) {
            throw new InvalidArgumentException('addRole takes a Role object as the parameter');
        }

        if (!$this->hasRole($role->getRole())) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * Pass a string, remove the Role object from collection.
     *
     * @param string $role
     *
     * @return GroupInterface
     */
    public function removeRole($role)
    {
        $roleElement = $this->getRole($role);
        if ($roleElement) {
            $this->roles->removeElement($roleElement);
        }

        return $this;
    }

    /**
     * Pass an ARRAY of Role objects and will clear the collection and re-set it with new Roles.
     *
     * @param Role[] $roles array of Role objects
     *
     * @return GroupInterface
     */
    public function setRoles(array $roles)
    {
        $this->roles->clear();
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Directly set the ArrayCollection of Roles. Type hinted as Collection which is the parent of (Array|Persistent)Collection.
     *
     * @param Collection $collection
     *
     * @return GroupInterface
     */
    public function setRolesCollection(Collection $collection)
    {
        $this->roles = $collection;

        return $this;
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
     *
     * @return GroupInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function isGroupValid(ExecutionContextInterface $context)
    {
        if (!(count($this->getRoles()) > 0)) {
            $context
                ->buildViolation('errors.group.selectone', array())
                ->atPath('rolesCollection')
                ->addViolation();
        }
    }
}
