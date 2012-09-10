UPGRADE FROM 1.3.2 TO 1.3.3
===========================

### AbstractAdminListConfigurator

  * There is a new method called 'getPermissionDefinition()'

    This method should return either null or a PermissionDefinition object that will be used in calls (by AdminList)
    to an AclHelper, applying ACL constraints you want to impose. When you return null, no restrictions will be applied.

### AdminList

  * There is a new method called 'setAclHelper()' & 'getAclHelper()'

    The setter method will allow you to set an AclHelper to be used to apply ACL constraints. If it is not set,
    no restrictions will be imposed, even if a PermissionDefinition was set (and vice versa).


UPGRADE FROM 1.1 TO 1.2
=======================

### BC BREAK AbstractAdminListConfigurator

  * There is a new abstract method here called 'getIndexUrlFor()'

    All AdminList configurations inheriting directly from this class must implement that method.
    It is used to redirect the user back to a list overview
    after an entity action has taken place (edit/delete/...)