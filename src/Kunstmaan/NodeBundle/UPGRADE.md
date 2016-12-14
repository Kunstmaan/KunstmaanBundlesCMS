Upgrade Instructions
====================

## To v2.2 with nested set support

To upgrade from a previous version, you have to copy the Doctrine migrations file from this bundle (Resources/DoctrineMigrations/Version20130611131506.php)
to your app/DoctrineMigrations/ folder and run it: ```bin/console doctrine:migrations:migrate```

This migration will:
* create some new columns in the kuma_node table
* create a stored procedure to rebuild the nested tree (you can call this procedure at any time to rebuild a corrupted tree)
* run the store procedure to update the current records in the node table

Note: make sure your database user has privileges to create and run a stored procedure.


## To v2.0+
The Node services now all have kunstmaan_node as prefix.

## All versions up to v1.3.6 -> v1.3.6+

The NodeMenu constructor has been changed to :
```public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclHelper $aclHelper, $lang, Node $currentNode = null, $permission = 'VIEW', $includeoffline = false, $includehiddenfromnav = false)```

The AclHelper is a service, you can get it in a controller using : ```$aclHelper = $this->container->get('kunstmaan.acl.helper');```

A command to create a basic set of permissions is also available, running ```bin/console kuma:init:acl```
(AFTER running ```bin/console init:acl```) will create a basic set of permissions for the nodes.
