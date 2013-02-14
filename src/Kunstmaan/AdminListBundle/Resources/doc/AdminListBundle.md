# AdminList

## Create your own AdminList

### Using a Generator

The [KunstmaanGeneratorBundle](https://github.com/Kunstmaan/KunstmaanGeneratorBundle) offers a generator to generate an AdminList for your entity. It will generate the required classes and settings based on your Entity class.

For more information, see the AdminList generator [documentation](https://github.com/Kunstmaan/KunstmaanGeneratorBundle/blob/master/Resources/doc/GeneratorBundle.md#adminlist).

### Manually

While we supply a generator to generate an AdminList, you can also create one following the following [documentation](https://github.com/Kunstmaan/KunstmaanAdminListBundle/edit/master/Resources/doc/AdminListBundle.md).

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
