UPGRADE FROM 5.8 to 5.9
=======================

AdminlistBundle
------------

* Using the `setObjectManager`, `setThreshold` and `setLockEnabled` methods of `Kunstmaan\AdminListBundle\Service\EntityVersionLockService` is deprecated, use the constructor to inject the required values instead.

DashboardBundle
------------

* Passing a command classname for the "$command" argument in `Kunstmaan\DashboardBundle\Widget\DashboardWidget::__construct` is deprecated and will not be allowed 6.0. Pass a command name instead.
* Using the `kunstmaan_dashboard.widget.googleanalytics.command` parameter to modify the `kunstmaan_dashboard.widget.googleanalytics` service is deprecated and the parameter will be removed in 6.0. Use service decoration or a service alias instead.

NodeSearchBundle
------------

* Instantiating the `Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage` class is deprecated and will be made abstract. Extend your implementation from this class instead.
