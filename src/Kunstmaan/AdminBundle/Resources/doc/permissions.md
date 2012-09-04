# Permissions

## Initialization and bootstrapping
First we have to configure the connection that the ACL system is supposed to use :
```yaml
# app/config/security.yml
security:
    acl:
        connection: default
```

After the connection is configured, we have to import the database structure, you do that by running
```app/console init:acl```

Without this you will not be able to use ACL permission support.


## MaskBuilder
Since we use a specific set of permissions, we created a custom MaskBuilder (Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder) and
PermissionMap (Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap) that support these permissions.

_Note:_ The CREATE permission has been commented out since we don't use this just yet, but might do so in the near future.

Using the MaskBuilder to create a set of permissions to be applied is very easy. Suppose you want to create a mask
that grants the VIEW, EDIT and PUBLISH permission, to do that you would use :
```php
$builder = new MaskBuilder();
$builder
    ->add('view')
    ->add('edit')
    ->add('publish');

$mask = $builder->get();
```

Constants are also defined for every permission, and since the mask is a bitmapped field, the above snippet could also
be written as follows :
```php
$mask = MaskBuilder::MASK_VIEW | MaskBuilder::MASK_EDIT | MaskBuilder::MASK_PUBLISH;
```

You can also use the MaskBuilder to remove a specific permission from a mask :
```php
$builder = new MaskBuilder($oldMask);
$builder
    ->remove('publish');

$newMask = $builder->get();
```

## Creating an ACL
To create a new ACL and grant the VIEW permission to the guest role for a domain object (ie. an entity) you would use :
```php
$aclProvider = $this->get('security.acl.provider');
$strategy = $this->get('security.acl.object_identity_retrieval_strategy');
$objectIdentity = $strategy->getObjectIdentity($entity);
$acl = $aclProvider->createAcl($objectIdentity);

$securityIdentity = new RoleSecurityIdentity('ROLE_GUEST');
$acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);

$aclProvider->updateAcl($acl);
```

Note that we use a custom object identity retrieval strategy (which is needed for Symfony 2.0 when using Doctrine Proxy
objects, refer to http://stackoverflow.com/questions/7476552/doctrine-2-proxy-classes-breaking-symfony2-acl for more info).

To retrieve the ACL for a specific domain object you would use :
```php
$aclProvider = $this->get('security.acl.provider');
$strategy = $this->get('security.acl.object_identity_retrieval_strategy');
$objectIdentity = $strategy->getObjectIdentity($entity);
$acl = $aclProvider->findAcl($objectIdentity);

// You can modify the ACL here
```

_Note:_ We currently only use object scope and role based permissions, you could however also use class scope and/or user
based permissions if needed.

## Checking access using ACL
To check if the current user has access to a specific domain object, you use the following :
```php

$securityContext = $this->get('security.context');

if (false === $securityContext->isGranted('VIEW', $entity))
{
    throw new AccessDeniedException();
}

...
```
In this example, we check whether the user has the VIEW permission and if the user doesn't have the VIEW permission
an exception will be thrown.

## Twig support
You can also check the permissions in Twig templates which can be useful if you want to only show specific front-end
elements for users that have a specific permission :

```php
{% if is_granted('DELETE', node) %}
    <button type="button" data-toggle="modal" data-target="#delete-page-modal" class="btn">Delete</button>
{% endif %}
```
In this example we check if the user has the DELETE permission for the node object, and only display the Delete button
if he has.

## QueryBuilder support
If you want to perform queries for entities that have ACL attached to them (ie. to display them in a list, but limited
to those entities for which you have a specific permission) you can use the AclHelper. There is one important caveat
though : the domain object used for the permission check should be the root entity of the QueryBuilder you provide.

To adapt a query in an EntityRepository method you could use:
```php
public function findAllWithPermission(AclHelper $aclHelper, array $permissions)
{
    $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->where('b.deleted = 0');
    $query = $aclHelper->apply($qb, $permissions);

    return $query->getResult();
}

```

The AclHelper is provided as a service, so to call the above method to check the VIEW permission in a controller
you could use :
```php
$aclHelper = $this->get('admin.acl.helper');
$em = $this->getDoctrine()->getManager();
$items = $em->getRepository('ARepository')->findAllWithPermission($aclHelper, array('view'));
```

## References

- [How to use Access Control Lists (ACLs)](http://symfony.com/doc/current/cookbook/security/acl.html)
- [How to use Advanced ACL Concepts](http://symfony.com/doc/current/cookbook/security/acl_advanced.html)
