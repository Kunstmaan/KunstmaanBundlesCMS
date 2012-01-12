<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * omnext group permissions
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\PermissionRepository")
 * @ORM\Table(name="permissions")
 */
class Permission
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $refId;

    /**
     * @ORM\Column(type="string")
     */
    protected $refEntityname;

    /**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="refGroup", referencedColumnName="id")
     */
    protected $refGroup;

    /**
     * @ORM\Column(type="string")
     */
    protected $permissions;

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
     * Get refId
     *
     * @return integer
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * Set refId
     *
     * @param string $refId
     */
    public function setRefId($num)
    {
        $this->refId = $num;
    }

    /**
     * Set refEntityname
     *
     * @param string $refEntityname
     */
    public function setRefEntityname($refEntityname)
    {
        $this->refEntityname = $refEntityname;
    }

    /**
     * Get refEntityname
     *
     * @return string
     */
    public function getRefEntityname()
    {
        return $this->refEntityname;
    }


    /**
     * Get refGroup
     *
     * @return integer
     */
    public function getRefGroup()
    {
        return $this->refGroup;
    }

    /**
     * Set refGroup
     *
     * @param string $refGroup
     */
    public function setRefGroup($refGroup)
    {
        $this->refGroup = $refGroup;
    }

    /**
     * Set title
     *
     * @param string $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getPermissions()
    {
        return $this->permissions;
    }


    public function setPermission($permission, $allow)
    {
        $permissions = $this->getPermissionsAsArray();
        $permissions[$permission] = (int)$allow;

        $this->setPermissions($this->getPermissionsByArray($permissions));
    }

    public function hasPermission($permission)
    {
        return (isset($this->permissions) && stripos($this->permissions, '|'.$permission.':1|') !== false);
    }


    public function canRead()
    {
        return $this->hasPermission('read');
    }


    public function canWrite()
    {
        return $this->hasPermission('write');
    }


    public function canDelete()
    {
        return $this->hasPermission('delete');
    }


    public function getPermissionsAsArray()
    {
        $permissionsArray = array();

        if(isset($this->permissions)) {
            $permissions = trim($this->permissions, '|');
            $permissions = explode('|', $permissions);
            foreach($permissions as &$permission) {
                list($key, $value) = explode(':', $permission);
                $permissionsArray[$key] = $value;
            }
        }

        return $permissionsArray;
    }

    public function getPermissionsByArray($permissionsArray)
    {
        $permissions = array();
        foreach($permissionsArray as $permission => $value) {
            $permissions[] = $permission.':'.$value;
        }
        $permissions = '|'.implode('|', $permissions).'|';

        return $permissions;
    }
}