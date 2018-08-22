# NodeBundle

## Nodes

The Node consists out of 4 elements. You have the actual [Node](https://github.com/Kunstmaan/KunstmaanNodeBundle/blob/master/Entity/Node.php) which contains multiple translations [NodeTranslation](https://github.com/Kunstmaan/KunstmaanNodeBundle/blob/master/Entity/NodeTranslation.php).
For each language your Node will have a [NodeTranslation](https://github.com/Kunstmaan/KunstmaanNodeBundle/blob/master/Entity/NodeTranslation.php). This allows for your node to work independently from other languages. Your node can be online in English, but still offline in French. The NodeTranslation will also contain the 'Title' and 'Slug' for your Node for that language.
The NodeTranslation holds your node's versions inside a language. We currently have 2 version states : 'Draft' and 'Public'. The public version will be publicly accessible when online, the draft version won't. You can have a draft version while there's still a public version online. Because of this, you can create a new version of your page while the old one is still available online. When your new version is ready, all you have to do to set it publicly accessible and replace the old version is to publish it.
The [NodeVersion](https://github.com/Kunstmaan/KunstmaanNodeBundle/blob/master/Entity/NodeVersion.php) holds information regarding the owner (the user who created this version), the time of creation and last update, the original version this version originated from and a reference to the Entity it holds. For each new version, a clone of your entity will be made.

The standard usage for Node is as the page in your website. The bundle contains a '[AbstractPage](https://github.com/Kunstmaan/KunstmaanNodeBundle/blob/master/Entity/AbstractPage.php)' class which can be extended to create your own pages. Each Node will have a reference to its parent and as such you can create a whole tree structure in your website's navigation.

## Retrieval

TODO : Documentation on how to retrieve certain nodes, node translations and node versions.

## Events

TODO : Add documentation on which events exists and how they can be used.

## Slugs & Router

TODO : Write down how slugs and the router works.

## Publish later

Nodes can be published on a specified date, you have to configure a cronjob for this:
```cron
* * * * * /....../bin/console kuma:nodes:cron
```
