# NodeBundle

## Configurable Action Menu

Configuring the action or sub action menu of pages is simply a matter of connecting an event listener
to the *KunstmaanNodeBundle/Helper/Event/Events::CONFIGURE_ACTION_MENU* or the
*KunstmaanNodeBundle/Helper/Event/Events::CONFIGURE_SUB_ACTION_MENU* event:

```yaml
# src/Acme/MainBundle/Resources/config/services.yml
services:
    acme_hello.configure_sub_actions_menu_listener:
        class: Acme\MainBundle\EventListener\ConfigureActionsMenuListener
        arguments: ["@doctrine.orm.entity_manager", "@router"]
        tags:
            - { name: 'kernel.event_listener', event: 'kunstmaan_node.configureSubActionMenu', method: 'onSubActionMenuConfigure' }
```

and in the listener function you can now modify the menu:

```php
/**
 * @param \Kunstmaan\NodeBundle\Event\ConfigureActionMenuEvent $event
 */
public function onSubActionMenuConfigure(ConfigureActionMenuEvent $event)
{
    $menu = $event->getMenu();
    $activeNodeVersion = $event->getActiveNodeVersion();

    if (!is_null($activeNodeVersion)) {
        $menu->addChild('subaction.hello_world', array('uri' => $this->router->generate('AcmeMainBundle_Hello_World')));
    }
}
```

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

## Known Issues

* Even though you click on 'Save as draft' after creating and modifying pageparts on your public version, the modifications will also be saved to the public version. It is advised to first "Save as draft" before you start altering pageparts.

## Publish later

Nodes can be published on a specified date, you have to configure a cronjob for this:
```cron
* * * * * /....../app/console kuma:nodes:cron
```

## Creating Nodes in Your Code

We've provided a ```PageCreatorService``` that simplifies the creation of nodes with multiple translations.
You can use this class in your own code like controllers, service methods in your Entities but perhaps most notably
in migrations. You can implement the ContainerAwareInterface in your migrations and fetch the service from there.

A sample of how you would create a page with an internal name, hooked right under the homepage,
for dutch and english and publish it immediately.

```
        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $overviewPage = new ContentPage();
        $overviewPage->setTitle('My Satellites');

        $translations = [];
        $translations[] = ['language' =>  'en', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('My collection of satellites');
            $translation->setSlug('my-collection-of-satellites');
        }];
        $translations[] = ['language' =>  'nl', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Mijn collectie satellieten');
            $translation->setSlug('mijn-collectie-satellieten');
        }];

        $options = [
            'parent' => $homePage,
            'page_internal_name' => 'satellites',
            'set_online' => true,
            'creator' => 'Admin'
        ];

        $pageCreator->createPage($overviewPage, $translations, $options);
```

Check the ```PagePartBundle``` documentation for a service that does something similar but for adding pageparts to a page.
