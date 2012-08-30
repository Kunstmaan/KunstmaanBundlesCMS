Configurable Action Menu
========================
Configuring the action or sub action menu of pages is simply a matter of connecting an event listener
to the *KunstmaanAdminNodeBundle/Helper/Event/Events::CONFIGURE_ACTION_MENU* or the
*KunstmaanAdminNodeBundle/Helper/Event/Events::CONFIGURE_SUB_ACTION_MENU* event:

```yaml
# src/Acme/MainBundle/Resources/config/services.yml
services:
    acme_hello.configure_sub_actions_menu_listener:
        class: Acme\MainBundle\EventListener\ConfigureActionsMenuListener
        arguments: ["@doctrine.orm.entity_manager", "@router"]
        tags:
            - { name: kernel.event_listener, event: adminnode.configureSubActionMenu, method: onSubActionMenuConfigure }
```

and in the listener function you can now modify the menu:

```php
/**
 * @param \Kunstmaan\AdminNodeBundle\Helper\Event\ConfigureActionMenuEvent $event
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