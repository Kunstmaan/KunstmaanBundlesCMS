#More information

We have implemented some new data collectors specific for the kunstmaan bundles. Those data collectors will put there information in the syfmony debug toolbar
when you are in debug mode. When you are on a production website and you are logged in to the CMS, there will be a new toolbar available similar to the web debug toolbar but only with our custom data collectors.

There are 3 data collectors by default:

- BundleVersionDataCollector

This will collect information about the current version of the bundles and will show the status in the toolbar.

- TranslatorDataCollector

This will collect the current translation keys that are used on the current page. You can click trough to the translation page directly.

- NodeDataCollector

This will show you an edit link in the toolbar. You can easily open the current node with one click.


### Enabling the custom toolbar ###

```
kunstmaan_admin:
    enable_toolbar_helper: true

```

### Adding own data collectors is easy ###

To implement your own data collectors you can add a new service that extends the Symfony\Component\HttpKernel\DataCollector\DataCollector and implements the DataCollectionInterface

Just add the following tag to your service:

```
    tags:
        - { name: kunstmaan_admin.toolbar_collector, template: 'KunstmaanNodeBundle:Toolbar:node.html.twig', id: kuma_node}
```

Take a look at the default data collectors to get started.