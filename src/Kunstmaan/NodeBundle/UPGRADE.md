Upgrade Instructions
====================

## To v2.0+
The Node services now all have kunstmaan_node as prefix.

## All versions up to v1.3.6 -> v1.3.6+

The NodeMenu constructor has been changed to :
```public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclHelper $aclHelper, $lang, Node $currentNode = null, $permission = 'VIEW', $includeoffline = false, $includehiddenfromnav = false)```

The AclHelper is a service, you can get it in a controller using : ```$aclHelper = $this->container->get('kunstmaan.acl.helper');```

A command to create a basic set of permissions is also available, running ```app/console kuma:init:acl```
(AFTER running ```app/console init:acl```) will create a basic set of permissions for the nodes.
