# Customizing the menu of the admin interface

Customizing the menu of the admin interface can be achieved by creating a service which implements the `Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface`. The service you created should provide an implementation for the `adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)` function.

In the adaptChildren function it is possible to customize the children for the given parent by adding or removing menu items in the children array. There are different types of menu items:

* `Kunstmaan\AdminBundle\Helper\Menu\MenuItem`: Which represents a menu item that will be shown in the tree
* `Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem`: Which represents a menu item that will be shown in the top menu

When the service is created, you still need to tag the service with the 'kunstmaan_admin.menu.adaptor' tag. An example of this is the SettingsMenuAdaptor:

```yaml
kunstmaan_admin.menu.adaptor.settings:
        class: Kunstmaan\AdminBundle\Helper\Menu\SettingsMenuAdaptor
        tags:
            -  { name: 'kunstmaan_admin.menu.adaptor' }
```
