# AdminList

## Permission support

### AbstractAdminListConfigurator

  * There is a new method called 'getPermissionDefinition()' (and a matching setter 'setPermissionDefinition()')

    This method should return either null or a PermissionDefinition object that will be used in calls (by AdminList)
    to an AclHelper, applying ACL constraints you want to impose. When you return null (the default return value),
    no restrictions will be applied.

### AdminList

  * There is a new method called 'setAclHelper()' & 'getAclHelper()'

    The setter method will allow you to set an AclHelper to be used to apply ACL constraints. If it is not set,
    no restrictions will be imposed, even if a PermissionDefinition was set (and vice versa).
