UPGRADE FROM 5.8 to 5.9
=======================

General
-------

* All event classes are marked as final.

AdminlistBundle
------------

* Using the `setObjectManager`, `setThreshold` and `setLockEnabled` methods of `Kunstmaan\AdminListBundle\Service\EntityVersionLockService` is deprecated, use the constructor to inject the required values instead.

DashboardBundle
------------

* Passing a command classname for the "$command" argument in `Kunstmaan\DashboardBundle\Widget\DashboardWidget::__construct` is deprecated and will not be allowed 6.0. Pass a command name instead.
* Using the `kunstmaan_dashboard.widget.googleanalytics.command` parameter to modify the `kunstmaan_dashboard.widget.googleanalytics` service is deprecated and the parameter will be removed in 6.0. Use service decoration or a service alias instead.

GeneratorBundle
------------

* The "kuma:generate:bundle" command and related classes is deprecated and will be removed in 6.0
* The "kuma:generate:entity" command and related classes is deprecated and will be removed in 6.0, use the "make:entity" command of the symfony/maker-bundle.

NodeSearchBundle
------------

* Instantiating the `Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage` class is deprecated and will be made abstract. Extend your implementation from this class instead.

UserManagementBundle
------------

* Overriding the user adminlist configurator class with `kunstmaan_user_management.user_admin_list_configurator.class` is deprecated, use the `kunstmaan_user_management.user.adminlist_configurator` config instead.
