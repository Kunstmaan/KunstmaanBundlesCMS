# UPGRADE FROM 3.4 to 3.5

## `gedmo.listener.tree` service was removed from KunstmaanNodeBundle (BC breaking)

The service `gedmo.listener.tree` was removed from NodeBundle. To upgrade you need to place the service definition
in your application config:

```
# app/config/config.yml
services:
    gedmo.listener.tree:
        class: Gedmo\Tree\TreeListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ @annotation_reader ] ]
```


## The deprecated `security.context` service was replaced

The `security.context` service was replaced with the `security.token_storage` and `security.authorization_checker` service.
More information about this change: http://symfony.com/blog/new-in-symfony-2-6-security-component-improvements

You will only need to make changes when your code extends some functionality of the CMS that used the `security.context` service.


## Upgrade LiipImagineBundle from v0.20.2 to v1.4.3

It is not possible anymore to change the format of cached images with all versions that were released after v0.20.2 (see 
https://github.com/liip/LiipImagineBundle/issues/584). There is an issue on the LiipImagineBundle roadmap to fix this, 
but it will not be ready before the 2.0 release (see https://github.com/liip/LiipImagineBundle/issues/686). In the 
meanwhile we extended some services to implement a quick workaround so we can a least update the bundle to a recent
version. 

You should change the `liip_imagine` configuration and the routing when updating:

In your `config.yml` replace

```
liip_imagine:
    cache_prefix: uploads/cache
    driver: imagick
    data_loader: filesystem
    data_root: %kernel.root_dir%/../web
    formats : ['jpg', 'jpeg','png', 'gif', 'bmp']
```

with

```
liip_imagine:
    resolvers:
        default:
            web_path:
                cache_prefix: uploads/cache
    driver: imagick
    data_loader: default
```

and in your `routing.yml`

```
_imagine:
    resource: .
    type:     imagine
```

with

```
_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"
```


## getRequest() is marked as deprecated since version 2.4

In the AdminListController actions we have removed the default value for the request attribute. This needs to be passed
from the correct AbstractAdminListController.



## Passing arguments to form type is deprecated

In older symfony versions, the first argument of the createForm
function allowed a form type with extra parameters. As of symfony 2.7 we need to pass the fully qualified class name of a form as
the first argument. Therefore we added a new option to the AbstractAdminListConfigurator class named $typeOptions. In your AdminListController
 you can use the setAdminTypeOptions function to pass extra parameters to the form defaults options.



## AdaptSimpleFormEvent changes

We have made some changes to the AdaptSimpleFormEvent to allow the needed fully qualified form class names.
The second parameter now requires a string instead of a AbstractType. There is also a possibilty now to add a fourth parameter
with the default options you would like to pass to your form.

Here is a updated example of how to implement an AdaptSimpleFormEvent:

```
public function onAdaptSimpleFormEvent(AdaptSimpleFormEvent $event)
{
    $tabPane = new TabPane('TabPane', $event->getRequest(), $this->formFactory);

    $widget = new FormWidget();
    $widget->addType('Tab',$event->getFormType(),$event->getData(), $event->getOptions());

    $tabPane->addTab(new Tab('User', $widget));
    $tabPane->buildForm();

    $event->setTabPane($tabPane);
}
```
