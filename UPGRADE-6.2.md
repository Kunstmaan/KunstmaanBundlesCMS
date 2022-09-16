UPGRADE FROM 6.1 to 6.2
========================

General
-------

- We've deprecated our GroundControl FE build setup, this setup will be replaced by the default symfony webpack encore setup. 
  If you generate a new project the new webpack setup will be used by default. Projects using GroundControl should replace this 
  setup by symfony webpack encore. See the [related PR](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2981) for more information on this change.
  In Kunstmaan CMS 7.O all GroundControl related files and support in templates will be removed.
- The minimum supported PHP version is 8.0.
- The supported Symfony versions are 4.4 and 5.4.

AdminBundle
-----------

* Not passing a value for "$logoutUrlGenerator" in "Kunstmaan\AdminBundle\Helper\AdminPanel\DefaultAdminPanelAdaptor::__construct" is deprecated and will be required in 7.0.
* The `kunstmaan_admin.admin_password` configuration key has been deprecated as it's not used anywhere, remove it from your config.
* The `Kunstmaan\AdminBundle\Form\ColorType` and `Kunstmaan\AdminBundle\Form\RangeType` form types are deprecated and will be removed in 7.0.
  Use the standard symfony form types `Symfony\Component\Form\Extension\Core\Type\ColorType` and `Symfony\Component\Form\Extension\Core\Type\RangeType` instead.

AdminListBundle
-----------

* The `Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter` class is deprecated and will be removed in 7.0. Use `Pagerfanta\Doctrine\DBAL\QueryAdapter` of the `pagerfanta/doctrine-dbal-adapter` package instead.

GeneratorBundle
---------------

* The following methods are deprecated and will be removed in 7.0. `Kunstmaan\GeneratorBundle\Generator\ArticleGenerator::generateServices`, 
  `Kunstmaan\GeneratorBundle\Generator\ArticleGenerator::generateRouting`, `Kunstmaan\GeneratorBundle\Generator\DefaultSiteGenerator::generateRouting`
  and `Kunstmaan\GeneratorBundle\Generator\DefaultSiteGenerator::generateConfig`
