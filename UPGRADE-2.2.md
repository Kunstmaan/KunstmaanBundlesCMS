# UPGRADE FROM 1.1 TO 1.2

## AdminListBundle

### BC BREAK AbstractAdminListConfigurator

There is a new abstract method here called 'getIndexUrlFor()'

All AdminList configurations inheriting directly from this class must implement that method. It is used to redirect the user back to a list overview after an entity action has taken place (edit/delete/...)

## NodeBundle

### All versions up to v1.3.6 -> v1.3.6+

The NodeMenu constructor has been changed to :
```public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclHelper $aclHelper, $lang, Node $currentNode = null, $permission = 'VIEW', $includeoffline = false, $includehiddenfromnav = false)```

The AclHelper is a service, you can get it in a controller using : ```$aclHelper = $this->container->get('kunstmaan.acl.helper');```

A command to create a basic set of permissions is also available, running ```app/console kuma:init:acl```
(AFTER running ```app/console init:acl```) will create a basic set of permissions for the nodes.
