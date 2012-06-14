<?php

namespace Kunstmaan\AdminBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * omnext user, can have different roles
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Kunstmaan\AdminBundle\Entity\Group")
     * @ORM\JoinTable(name="user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;


    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->groups = new ArrayCollection();
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
    
    /**
     * Set id
     *
     * @param id integer
     */
    public function setId($id)
    {
    	$this->id = $id;
    }

    /**
     * Gets the groupIds for the user.
     *
     * @return array
     */
    public function getGroupIds()
    {
        $groups = $this->groups;

        $groupIds = array();
        if(count($groups) > 0) {
            foreach($groups as $group) {
                $groupIds[] = $group->getId();
            }
        }

        return $groupIds;
    }

    /**
     * Gets the groups the user belongs to.
     * 
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     * @return Boolean
     */
    public function hasRole($role)
    {
        if (isset($role)) {
            foreach ($this->getRoles() as $r) {
                if (is_string($r) && $r === $role) {
                    return true;
                }
                if (is_object($r) && $r instanceof Role && $r->getRole() === $role) {
                    return true;
                }
            }
        }
        return false;
    }
}