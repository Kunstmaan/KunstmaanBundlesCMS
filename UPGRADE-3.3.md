# UPGRADE FROM 3.2 to 3.3

## NodeMenu refactored to service (BC breaking)

In order to add the multi-domain and multi-site support we had to refactor the NodeMenu. It has become
a service. If you used it anywhere in your code, you will have to adapt your code.

Replace any instance of ```$nodeMenu = new NodeMenu(...);``` in your code with the following snippet :

```php
    $nodeMenu = $container->get('kunstmaan_node.node_menu');
    $nodeMenu->setLocale(...);
    $nodeMenu->setCurrentNode(...);
    $nodeMenu->setIncludeOffline(...);
    $nodeMenu->setIncludeHiddenFromNav(...);
```

Only use the setters you really need (locale & current node are probably always needed).


## New HomePageInterface

If you want to be able to add a homepage in the backend, you should make sure that your HomePage entity implements the
HomePageInterface class. When you login as super admin (ie. a user that has ROLE_SUPER_ADMIN) you will be able to see
the 'Add homepage' button at the top of the pages list.

Only the page types that implement the HomePageInterface can be selected for creating a new homepage.


## New DomainConfiguration service

If you want to get domain related data for the current host, you can access the kunstmaan_admin.domain_configuration
service.

Refer to the DomainConfigurationInterface to see what method calls are available.


## root_id was added to node search index

To support multi-site, we added the root_id field to the default node search index. It contains the node id of the
site's root page which makes it easier to filter search results for every site separately. The default search has
been modified so only search results from the current site are shown.

*Note*: If you are using search at the moment you should run ```app/console kuma:search:populate full``` after
upgrading to make sure your search index is updated accordingly!


## AdminPanelActions were added

In order to make it easier to add new actions to the admin panel (located on the right side of the admin menu bar), we
created the AdminPanelAction class. If you wish to add your own custom actions you can create an AdminPanelAdaptor service
which implements the AdminPanelAdaptorInterface and is tagged with ```name: 'kunstmaan_admin.admin_panel.adaptor'```
to add them.

Refer to the DefaultAdminPanelAdaptor source to see how this is done.


## New Twig functions

- ```get_locales()``` - returns the public (front-end) locales for the current host
- ```get_backend_locales()``` - returns the admin (back-end) locales for the current host

For multi-site / multi-domain you should make sure you no longer use the ```requiredlocales``` in your Twig templates.


## Custom route loaders not possible anymore when they use a page controller (BC breaking)

It is not possible anymore to use custom route loaders (`LoaderInterface`) for defining extra routes
that are handled via the controller of a `Page`. You should use a custom router (`RouterInterface`) instead
to define the route, and put the `_nodeTranslation` variable in the `_route_params` in the match function.


## Custom configuration for ckEditors (BC breaking)

It is now possible (again) to override the complete configuration of a ckEditor where in previous versions you could only override the toolbar. This means
that a custom config should now be an object, instead of an array.


## Add extra plugins to ckEditor

It is now possible to add extra plugins to ckEditor. These are also defined in `_ckeditor_configs.html.twig`
In order to add extra plugins you can add arrays to the externalPlugins array like this:  
```externalPlugins = [
	['names', 'path-to-plugin(s)', 'optional-filename']
];```

These will then be loaded on ckEditor's plugins.addExternal method and become available for configuration.
