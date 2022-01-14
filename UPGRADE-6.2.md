UPGRADE FROM 6.1 to 6.2
========================

AdminBundle
-----------

* Not passing a value for "$logoutUrlGenerator" in "Kunstmaan\AdminBundle\Helper\AdminPanel\DefaultAdminPanelAdaptor::__construct" is deprecated and will be required in 7.0.

AdminListBundle
-----------

* The `Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter` class is deprecated and will be removed in 7.0. Use `Pagerfanta\Doctrine\DBAL\QueryAdapter` of the `pagerfanta/doctrine-dbal-adapter` package instead.
