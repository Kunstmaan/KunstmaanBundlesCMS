UPGRADE FROM 5.1 to 5.2
=======================

General
-------

 * We don't depend on the `symfony/symfony` package anymore, instead the individual `symfony/*` packages are added as dependencies.
   If your code depends on other symfony packages than the ones we require, add them to your project `composer.json`.
 * The `symfony/monolog-bundle` package was removed as it was no dependency of the kunstmaan cms. If you use this in your project, add the `"symfony/monolog-bundle": "~2.8|~3.0"` constraint to your project `composer.json`.

AdminBundle
-----------

* We've removed the `RoleInterface` on the `Kunstmaan\AdminBundle\Entity\Group` entity if you run your code on symfony 4. 
  The interface was deprecated and removed in symfony 4. If you used this interface to check the `Group` entity change it to
  the `FOS\UserBundle\Model\GroupInterface`. The `Group` entity won't change if you run on symfony 3.4 but it's adviced to make 
  this change already.

NodeSearchBundle
----------------

* Depending on the service container to retrieve searchers is deprecated and will be removed in 6.0. Tag your custom node
  searchers with the "kunstmaan_node_search.node_searcher" tag, to have them available for the NodeSearchBundle.

NodeBundle
----------

 * Added the ability to split up an entity into multiple tabs. See [docs/bundles/node-bundle/entity-tabs.md](docs/bundles/node-bundle/entity-tabs.md)

PagePartBundle
--------------

 * Added the `page_templates_dir` configuration option with a default value of `config/pagetemplates/` be set/used with symfony 4.