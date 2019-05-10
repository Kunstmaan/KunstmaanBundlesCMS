UPGRADE FROM 5.1 to 5.2
=======================

General
-------

 * We don't depend on the `symfony/symfony` package anymore, instead the individual `symfony/*` packages are added as dependencies.
   If your code depends on other symfony packages than the ones we require, add them to your project `composer.json`.
 * The `symfony/monolog-bundle` package was removed as it was no dependency of the kunstmaan cms. If you use this in your project, add the `"symfony/monolog-bundle": "~2.8|~3.0"` constraint to your project `composer.json`.
 * All service class name parameters have been deprecated for service definitions and will be removed in 6.0. Instead of overwriting the class name parameters to provide your own implementation, use service decoration or service aliases.
 * liip/imagine-bundle is upgraded to v2.0. Update your routing import from `resource: "@LiipImagineBundle/Resources/config/routing.xml` to `resource: "@LiipImagineBundle/Resources/config/routing.yaml`

AdminBundle
-----------

 * We've removed the `RoleInterface` on the `Kunstmaan\AdminBundle\Entity\Group` entity if you run your code on symfony 4. 
   The interface was deprecated and removed in symfony 4. If you used this interface to check the `Group` entity change it to
   the `FOS\UserBundle\Model\GroupInterface`. The `Group` entity won't change if you run on symfony 3.4 but it's adviced to make 
   this change already.
 * Setting the `multilanguage` directly is deprecated and support for this parameter will be removed in 6.0. Provide the `kunstmaan_admin.multi_language` config instead.
 * Setting the `requiredlocales` directly is deprecated and support for this parameter will be removed in 6.0. Provide the `kunstmaan_admin.required_locales` config instead.
 * Setting the `defaultlocale` directly is deprecated and support for this parameter will be removed in 6.0. Provide the `kunstmaan_admin.default_locale` config instead.
 * Setting the `websitetitle` directly is deprecated and support for this parameter will be removed in 6.0. Provide the `kunstmaan_admin.website_title` config instead.

MediaBundle
-----------

 * Not providing a value for the `kunstmaan_media.aviary_api_key` config while setting the `aviary_api_key` parameter is deprecated, this config value will replace the `aviary_api_key` parameter in KunstmaanMediaBundle 6.0.

DashboardBundle
---------------

 * Not providing a value for the `kunstmaan_dashboard.google_analytics.api.client_secret` config while setting the `google.api.client_secret` parameter is deprecated, this config value will replace the `google.api.client_secret` parameter in KunstmaanDashboardBundle 6.0.
 * Not providing a value for the `kunstmaan_dashboard.google_analytics.api.client_id` config while setting the `google.api.client_id` parameter is deprecated, this config value will replace the `google.api.client_id` parameter in KunstmaanDashboardBundle 6.0.
 * Not providing a value for the `kunstmaan_dashboard.google_analytics.api.app_name` config while setting the `google.api.app_name` parameter is deprecated, this config value will replace the `google.api.app_name` parameter in KunstmaanDashboardBundle 6.0.
 * Not providing a value for the `kunstmaan_dashboard.google_analytics.api.dev_key` config while setting the `google.api.dev_key` parameter is deprecated, this config value will replace the `google.api.dev_key` parameter in KunstmaanDashboardBundle 6.0.

NodeSearchBundle
----------------

 * Depending on the service container to retrieve searchers is deprecated and will be removed in 6.0. Tag your custom node
   searchers with the "kunstmaan_node_search.node_searcher" tag, to have them available for the NodeSearchBundle.
 * Passing the container as the first argument of `\Kunstmaan\NodeSearchBundle\EventListener\NodeIndexUpdateEventListener` is deprecated and will be removed in 6.0. Inject the `kunstmaan_node_search.search_configuration.node` service instead.

NodeBundle
----------

 * Added the ability to split up an entity into multiple tabs. See [docs/bundles/node-bundle/entity-tabs.md](docs/bundles/node-bundle/entity-tabs.md)

SearchBundle
------------

 * Not providing a value for the `kunstmaan_search.connection.host` config while setting the `kunstmaan_search.hostname` parameter is deprecated, this config value will replace the `kunstmaan_search.hostname` parameter in KunstmaanDashboardBundle 6.0.
 * Not providing a value for the `kunstmaan_search.connection.port` config while setting the `kunstmaan_search.port` parameter is deprecated, this config value will replace the `kunstmaan_search.port` parameter in KunstmaanDashboardBundle 6.0.
 * Not providing a value for the `kunstmaan_search.connection.username` config while setting the `kunstmaan_search.username` parameter is deprecated, this config value will replace the `kunstmaan_search.username` parameter in KunstmaanDashboardBundle 6.0.
 * Not providing a value for the `kunstmaan_search.connection.password` config while setting the `kunstmaan_search.password` parameter is deprecated, this config value will replace the `kunstmaan_search.password` parameter in KunstmaanDashboardBundle 6.0.
 * Not providing a value for the `kunstmaan_search.index_prefix` config while setting the `searchindexprefix` parameter is deprecated, this config value will replace the `searchindexprefix` parameter in KunstmaanDashboardBundle 6.0.

TranslatorBundle
----------------

 * `Kunstmaan\TranslatorBundle\Service\Command\DiffCommand` is deprecated and will be removed in KunstmaanTranslatorBundle 6.0.
 * `Kunstmaan\TranslatorBundle\Command\MigrationsDiffCommand` is deprecated and will be removed in KunstmaanTranslatorBundle 6.0.
