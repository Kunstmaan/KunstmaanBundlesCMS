<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Permission;

class PermissionDefinition
{
    private $entity = null;
    private $alias = null;
    private $permissions = array();

    /**
     * @param array       $permissions Set of permissions (strings)
     * @param string|null $entity      Class name of entity for which to check permissions
     * @param string|null $alias       DQL alias of entity table
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($permissions, $entity = null, $alias = null)
    {
        $this->setPermissions($permissions);
        $this->entity = $entity;
        $this->alias  = $alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param array $permissions
     *
     * @throws \InvalidArgumentException
     */
    public function setPermissions($permissions)
    {
        if (!is_array($permissions) || empty($permissions)) {
            throw new \InvalidArgumentException("You have to provide at least one permission!");
        }
        $this->permissions = $permissions;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

}