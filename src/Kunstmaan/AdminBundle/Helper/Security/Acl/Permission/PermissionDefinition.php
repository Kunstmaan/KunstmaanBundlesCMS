<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Permission;

use InvalidArgumentException;

/**
 * The PermissionDefinition object allows you to define the settings to be used by the ACL helper
 */
class PermissionDefinition
{
    /**
     * @var string
     */
    private $entity = null;

    /**
     * @var string
     */
    private $alias = null;

    /**
     * @var array
     */
    private $permissions = array();

    /**
     * Construct the PermissionDefinition
     *
     * @param array       $permissions Set of permissions (strings)
     * @param string|null $entity      Class name of entity for which to check permissions
     * @param string|null $alias       DQL alias of entity table
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $permissions, $entity = null, $alias = null)
    {
        $this->setPermissions($permissions);
        $this->entity = $entity;
        $this->alias = $alias;
    }

    /**
     * Set alias.
     *
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set entity.
     *
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get entity.
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set permissions.
     *
     * @param array $permissions
     *
     * @throws InvalidArgumentException
     */
    public function setPermissions(array $permissions)
    {
        if (!is_array($permissions) || empty($permissions)) {
            throw new InvalidArgumentException('You have to provide at least one permission!');
        }
        $this->permissions = $permissions;
    }

    /**
     * Get permissions.
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
