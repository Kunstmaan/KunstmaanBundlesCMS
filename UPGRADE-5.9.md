UPGRADE FROM 5.8 to 5.9
=======================

AdminlistBundle
------------

* Using the `setObjectManager`, `setThreshold` and `setLockEnabled` methods of `Kunstmaan\AdminListBundle\Service\EntityVersionLockService` is deprecated, use the constructor to inject the required values instead.

NodeSearchBundle
------------

* Instantiating the `Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage` class is deprecated and will be made abstract. Extend your implementation from this class instead.
