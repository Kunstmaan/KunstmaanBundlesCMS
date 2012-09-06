Upgrade Instructions
====================

## All versions up to v1.3.6 -> v1.3.6+

The NodeMenu constructor has been changed from :
```public function __construct($container, $lang, Node $currentNode = null, $permission = 'read', $includeoffline = false, $includehiddenfromnav = false)```
to
```public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclHelper $aclHelper, $lang, Node $currentNode = null, $permission = 'VIEW', $includeoffline = false, $includehiddenfromnav = false)```

The AclHelper is a service, you can get it in a controller using : ```$aclHelper = $this->container->get('kunstmaan.acl.helper');```
