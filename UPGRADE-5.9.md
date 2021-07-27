UPGRADE FROM 5.8 to 5.9
=======================

General
-------

* All event classes are marked as final.
* If you still require the `kunstmaan/bundles-cms` package, update you `composer.json` to require `babdev/pagerfanta-bundle`
  instead of `white-october/pagerfanta-bundle`. All specific bundle packages that use pagerfanta functionality are now requiring
  the specifc pagerfanta packages. Newer skeleton installs will require the correct pagerfanta bundle package but old projects
  or projects using the `white-october` package are encouraged to switch their dependencies.
* The abandoned package `fzaninotto/faker` is replaced with the `fakerphp/faker` package. If you use the abandoned package
  in your project, replace it with `fakerphp/faker` to allow upgrading to v5.9.

AdminBundle
------------

* The `kunstmaan_admin.admin_exception_excludes` option is deprecated. Use `kunstmaan_admin.exception_logging.exclude_patterns` instead.
* Exception logging in by the cms (and the linked exception module) can now be disabled with `kunstmaan_admin.exception_logging: false` or `kunstmaan_admin.exception_logging.enabled: false`.
* Using FosUserBundle as the authentication system for the cms login is deprecated and usages will be removed in 6.0. Use the replacement authentication system instead.
  To enable the new authentication (and disable any FosUserBundle usages) set the `kunstmaan_admin.authentication.enable_new_authentication` config to `true` (this option will always default to true in 6.0).

If you previously did an override of the user or group class, your config should be updated.

From:
```yaml
fos_user:
    user_class: App\Entity\User
    group_class: App\Entity\Group
```

To:
```yaml
kunstmaan_admin:
    authentication:
        user_class: App\Entity\User
        group_class: App\Entity\Group
```

Your `security.yaml` will also need an update after enabling the new authentication system. Adapt your config according to the following example,
which is the default `security.yaml` that ships with a new installation.

```yaml
security:
    encoders:
        Kunstmaan\AdminBundle\Entity\UserInterface: bcrypt
    ...

    providers:
        cms_users:
          entity: { class: Kunstmaan\AdminBundle\Entity\User, property: username }

    firewalls:
        main:
            pattern: .*
            form_login:
                login_path: kunstmaan_admin_login
                check_path: kunstmaan_admin_login
                provider: cms_users
            logout:
                path: kunstmaan_admin_logout
                target: KunstmaanAdminBundle_homepage
            ...

    access_control:
        ...
        - { path: ^/([^/]*)/admin/reset.*, role: IS_AUTHENTICATED_ANONYMOUSLY }
        ...
```
* The constructor arguments of `Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker` have changed, inject the correct/required services/parameters.

AdminlistBundle
------------

* Using the `setObjectManager`, `setThreshold` and `setLockEnabled` methods of `Kunstmaan\AdminListBundle\Service\EntityVersionLockService` is deprecated, use the constructor to inject the required values instead.

DashboardBundle
------------

* Passing a command classname for the "$command" argument in `Kunstmaan\DashboardBundle\Widget\DashboardWidget::__construct` is deprecated and will not be allowed 6.0. Pass a command name instead.
* Using the `kunstmaan_dashboard.widget.googleanalytics.command` parameter to modify the `kunstmaan_dashboard.widget.googleanalytics` service is deprecated and the parameter will be removed in 6.0. Use service decoration or a service alias instead.
* Not passing a value for the `$configHelper` or `$queryHelper` parameters in `Kunstmaan\DashboardBundle\Command\GoogleAnalyticsDataCollectCommand::__construct` is deprecated and the parameters will be required. Inject the required services instead.

GeneratorBundle
------------

* The "kuma:generate:bundle" command and related classes is deprecated and will be removed in 6.0
* The "kuma:generate:entity" command and related classes is deprecated and will be removed in 6.0, use the "make:entity" command of the symfony/maker-bundle.

NodeSearchBundle
------------

* Instantiating the `Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage` class is deprecated and will be made abstract. Extend your implementation from this class instead.

RedirectBundle
------------

* Overriding the redirect entity class with `kunstmaan_redirect.redirect.class` is deprecated, use the `kunstmaan_redirect.redirect_entity` config instead.

UserManagementBundle
------------

* Overriding the user adminlist configurator class with `kunstmaan_user_management.user_admin_list_configurator.class` is deprecated, use the `kunstmaan_user_management.user.adminlist_configurator` config instead.

MediaBundle
----------

* Not passing a value for the "$mediaPath" parameter of "\Kunstmaan\MediaBundle\Helper\File\FileHelper::__construct" is deprecated, a value will be required.
