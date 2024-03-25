# Configuring adminlists

The default adminlist allows you to add a new item to the list. It also allows you to modify or delete each item. If you want to customize your adminlist, you have to modify the adminlistConfigurator file of that adminlist.

## Adding or removing default actions

There are some simple methods build into the Kunstmaan CMS give you control over the default action buttons. You can disable them for your adminlist by overriding them in the adminlistConfigurator to false or true.

```PHP
canAdd()
{
	return true;
}

canEdit($item)
{
	return true;
}

canDelete($item)
{
	return true;
}

canExport()
{
	return false;
}
```
> Note the canExport() method. It is disabled by default. Your adminlist will have export capabilities by enabling it. (see the Exporting admin lists).

## Adding your own actions

There are actually three different approaches. The choice depends on the nature of your situation.

* List actions: applies an action to the whole list
* Item actions: applies an action to a specific list row
* Bulk actions: applies an action to multiple rows at once

### Adding an adminlist list action
Global list actions can be easily configured like this:

Open the needed file in your projects `Adminlist` folder and add the following method in the AdminlistConfigurator file:

```PHP
    public function buildListActions()
    {
        $listRoute = array(
            'path' => 'acmewebsitebundle_route_name',
            'params' => array()
        );
        $this->addListAction(new SimpleListAction($listRoute, 'Action_name', 'Action_icon'));
    }
```

`$listRoute` contains the name of the route you want to call. It has an array of parameters that will be posted to that route if you call the action. The second parameter is a string containing the name of the button. The third parameter contains the icon.

> The CMS uses [Font Awesome](https://fortawesome.github.io/Font-Awesome/icons/) as icon provider. Just pick a name from their list to use the icon on your action buttons.

### Adding an adminlist item action
Sometimes you need to apply a specific action to a row of an adminlist. This is where item actions come to the rescue.

Item actions can be configured by adding a method to the AdminlistConfigurator file:

```PHP
    public function buildItemActions()
    {
        /**
         * @param ItemType $item
         * @return array
         */
        $itemRoute = function ($item) {
            return array(
                'path'   => 'acmewebsitebundle_route_name',
                'params' => array(
                    'server' => $item->getId()
                )
            );
        };
        $this->addItemAction(new SimpleItemAction($itemRoute, 'Action_icon', 'Action_name'));
    }
```
In this example we pass the `Id` of the row along with the route so we know on which item we have to execute the action.
The name and the icon work the same way as the list action above.

### Adding adminlist bulk actions
Bulk actions are actions that can be applied to multiple adminlist items at once. If a bulk action is found, checkboxes will be placed before each item in the adminlist and bulk action buttons will be placed below the admin list.

To add a bulk action, add the following lines to your adminlistConfigurator `__construct` method

```PHP
public function __construct(EntityManagerInterface $em, AclHelper $aclHelper = null)
    {
        ...
        $bulkPath = array('path' => 'acmewebsitebundle_route_name', 'params' => array());
        $this->addBulkAction(new SimpleBulkAction($bulkPath, "Action_name", "Action_icon"));
    }
```

## Exporting adminlists
It's possible to export your adminlists. At this moment, you can export to a csv file or an excel file out of the box. To have an export button in your adminlists, make sure to have the `canExport()` method return `true`.

Its also possible to define custom export fields by adding the following to your adminlistConfigurator:

```PHP
    public function buildExportFields()
    {
       /**
     	* @param string $name     The field name
     	* @param string $header   The header title
     	* @param string $template The template
     	**/
		$this->addExportField($name, $header, $template = null)
    }
```

## Configuring the limits of pagination
You can provide your own limits for pagination of the adminlist by making your AdminlistConfigurator implement the ChangeableLimitInterface.

That interface requires you to implement a couple of methods to allow the
dynamic values for pagination. GetLimit, GetLimitOptions, BindRequest.

Luckily there also is a trait provided by us that does most of the heavy lifting allowing you to only overwrite the values or other things you might want to change.
The trait is called the ChangeableLimitTrait

No other work is needed to adapt the html. There a check is already made to see
if your AdminlistConfigurator implements the ChangeableLimitInterface.
