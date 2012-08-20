<?php

namespace Kunstmaan\AdminBundle\Permission;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\Permission;

use Kunstmaan\AdminBundle\Modules\ClassLookup;

/**
 * PermissionAdmin
 */
class PermissionAdmin
{

    protected $request      = null;
    protected $resource     = null;
    protected $em           = null;
    protected $possiblePermissions = null;
    protected $permissions = null;

    /**
     * @param object        $resource            The object which has the permissions
     * @param EntityManager $em                  The EntityManager
     * @param array         $possiblePermissions Possible permissions
     */
    public function initialize($resource, EntityManager $em, $possiblePermissions = array('read', 'write', 'delete'))
    {
        $this->em           = $em;
        $this->resource     = $resource;
        $this->possiblePermissions = $possiblePermissions;
        $this->permissions = $this->em->getRepository('KunstmaanAdminBundle:Permission')->findBy(array(
                'refId'         => $resource->getId(),
                'refEntityname' => ClassLookup::getClass($resource),
        ));
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param Group $group
     *
     * @return Permission
     */
    public function getPermission($group)
    {
        foreach ($this->permissions as &$permission) {
            if ($permission->getRefGroup() == $group) {
                return $permission;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAllGroups()
    {
        return $this->em->getRepository('KunstmaanAdminBundle:Group')->findAll();
    }

    /**
     * @return array
     */
    public function getPossiblePermissions()
    {
        return $this->possiblePermissions;
    }

    /**
     * @param Request $request
     *
     * @return boolean
     */
    public function bindRequest($request)
    {
        $this->request = $request;

        $postPermissions = $request->request->get('permissions');

        //Fetch all permissions for the object to loop trough them
        $dbPermissions = $this->em->getRepository('KunstmaanAdminBundle:Permission')->findBy(array(
            'refId'         => $this->resource->getId(),
            'refEntityname' => ClassLookup::getClass($this->resource),
        ));

        foreach ($this->getAllGroups() as $group) {
            $dbPermission = $this->getPermission($group);
            if ($dbPermission==null) {
                $dbPermission = new Permission();
                $dbPermission->setRefEntityname(ClassLookup::getClass($this->resource));
                $dbPermission->setRefId($this->resource->getId());
                $dbPermission->setRefGroup($group);
            }
            foreach ($this->possiblePermissions as $permission) {
                if (isset($postPermissions[$group->getId()][$permission])) {
                    $dbPermission->setPermission($permission, true);
                } else {
                    $dbPermission->setPermission($permission, false);
                }
            }
            $this->em->persist($dbPermission);
        }

        return true;
    }
}
