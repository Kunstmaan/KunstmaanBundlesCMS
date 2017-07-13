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

```
bin/console init:acl
```

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
$builder->remove('publish');
$newMask = $builder->get();
```

## Creating an ACL

To create a new ACL and grant the VIEW permission to ROLE_CUSTOM for a domain object (ie. an entity) you would use :

```php
$aclProvider = $this->get('security.acl.provider');
$strategy = $this->get('security.acl.object_identity_retrieval_strategy');
$objectIdentity = $strategy->getObjectIdentity($entity);
$acl = $aclProvider->createAcl($objectIdentity);
$securityIdentity = new RoleSecurityIdentity('ROLE_CUSTOM');
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
$authChecker = $this->get('security.authorization_checker');
if (false === $authChecker->isGranted(PermissionMap::PERMISSION_VIEW, $entity))
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

The basic configuration is set in a PermissionDefinition object. You should at least specify an array of permissions,
and can optionally also specify the root entity class name and the querybuilder alias of the root entity table. If
you don't specify the latter variables, the querybuilders' first root entity and alias will be used.

To adapt a query in an EntityRepository method you could use:

```php
public function findAllWithPermission(AclHelper $aclHelper, PermissionDefinition $permissionDef)
{
    $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->where('b.deleted = 0');
    $query = $aclHelper->apply($qb, $permissionDef);

    return $query->getResult();
}
```

The AclHelper is provided as a service, so to call the above method to check the VIEW permission in a controller
you could use :

```php
$aclHelper = $this->get('kunstmaan_admin.acl.helper');
$em = $this->getDoctrine()->getManager();
$permissionDef = new PermissionDefinition(array('view'));
$items = $em->getRepository('ARepository')->findAllWithPermission($aclHelper, $permissionDef);
```

## PermissionDefinition
The permission definition object allows you to define the settings to be used by the ACL helper. Per default
the (ORM based) AclHelper will check the QueryBuilder passed to it to determine the first root entity and
the corresponding alias. You can override this by providing *both* a root entity name and alias, ie. :

```php
$permissionDef = new PermissionDefinition(array('view'), 'Kunstmaan\NodeBundle\Entity\Node', 'n');
```

In the previous example, you force the Node object to be used as ACL root entity and make sure the 'n' alias
(that you defined in the QueryBuilder that you wish to modify) will be used to apply the ACL permissions.

The native AclHelper (called AclNativeHelper) will *always* need a root entity and alias set, because the
DBAL QueryBuilder doesn't know about your entities.

One important thing to note is that ACL permissions currently can only be applied to entities with a single
unique primary key (so in fact there's no support for composite keys).

## Kunstmaan bundles version check

All logged in admins with the `ROLE_SUPER_ADMIN` role will be able to see a page with a list of the used
Kunstmaan bundles with their version information. This list will indicate which bundles are up-to-date and
which can be updated.<br/>
If you don't want that the version check is performed periodically, you can disable it via your config.yml

```yaml
parameters:
    version_checker.enabled: false
```

## References

- [How to use Access Control Lists (ACLs)](http://symfony.com/doc/current/cookbook/security/acl.html)
- [How to use Advanced ACL Concepts](http://symfony.com/doc/current/cookbook/security/acl_advanced.html)
