<?php

namespace Kunstmaan\AdminBundle\Permission;

use Symfony\Component\Form\FormBuilder;
use Kunstmaan\AdminBundle\Modules\ClassLookup;


class PermissionAdmin {

    protected $request      = null;
    protected $resource     = null;
    protected $em           = null;
    protected $possiblePermissions = null;

    function initialize($resource, $em, $possiblePermissions = array('read', 'write', 'delete'))
    {
        $this->em           = $em;
        $this->resource     = $resource;
        $this->possiblePermissions = $possiblePermissions;
    }


    public function getPermissions()
    {
        $groups = $this->em->getRepository('KunstmaanAdminBundle:Permission')->findBy(array(
            'refId'         => $this->resource->getId(),
            'refEntityname' => ClassLookup::getClass($this->resource),
        ));

        return $groups;
    }


    /**
     * @param $request
     */
    public function bindRequest($request){
        $this->request = $request;

        $postPermissions = $request->request->get('permissions');

        //Fetch all permissions for the object to loop trough them
        $dbPermissions = $this->em->getRepository('KunstmaanAdminBundle:Permission')->findBy(array(
            'refId'         => $this->resource->getId(),
            'refEntityname' => ClassLookup::getClass($this->resource),
        ));

        foreach($dbPermissions as $dbPermission) {
            $group = $dbPermission->getRefGroup();

            foreach($this->possiblePermissions as $permission){
                if(isset($postPermissions[$group->getId()][$permission])) {
                    $dbPermission->setPermission($permission, true);
                } else {
                    $dbPermission->setPermission($permission, false);
                }
            }
            $this->em->persist($dbPermission);
        }
        //$this->em->flush();

        return true;
    }
}
