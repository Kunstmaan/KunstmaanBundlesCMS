UPGRADE FROM 5.0 to 5.1
=======================

AdminBundle
-----------

 * Passing the `logger` service as the second argument in `Kunstmaan\AdminBundle\Toolbar\BundleVersionDataCollector` is deprecated and will be removed in 6.0.
 * Injecting the container in the `DomainConfiguration` is deprecated and will be removed in 6.0. Inject the required parameters instead.

AdminListBundle
---------------

 * Getting services directly from the container in list controllers is deprecated and will be removed in 6.0. Register your controllers as services and inject the necessary dependencies.
 * Getting parameters directly from the container in list controllers is deprecated and will be removed in 6.0. Register your controllers as services and inject the necessary parameters.

ArticleBundle
-------------

 * Passing the `request_stack` service as the third argument in `Kunstmaan\ArticleBundle\Twig\ArticleTwigExtension` is deprecated and will be removed in 6.0.
 * The `getAdminListConfigurator` and `createAdminListConfigurator` method in the `AbstractArticleEntityAdminListController` class will change from `public` to `protected` visibility in 6.0.

ConfigBundle
------------

 * Passing the `container` as the sixth argument in `Kunstmaan\ConfigBundle\Controller\ConfigController` is deprecated in and will be removed in 6.0.

MediaBundle
-----------

 * The unused `MediaController::moveMedia` action is deprecated and will be removed in 6.0.
 * `CleanDeletedMediaCommand::__construct()` now takes an instance of `Doctrine\ORM\EntityManagerInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `CreatePdfPreviewCommand::__construct()` now takes an instance of `Doctrine\ORM\EntityManagerInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `RebuildFolderTreeCommand::__construct()` now takes an instance of `Doctrine\ORM\EntityManagerInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `RenameSoftDeletedCommand::__construct()` now takes an instance of `Doctrine\ORM\EntityManagerInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `CleanDeletedMediaCommand`, `CreatePdfPreviewCommand`, `RebuildFolderTreeCommand` and `RenameSoftDeletedCommand` have been marked as final.

MultiDomainBundle
-----------------

* Injecting the container in the `DomainConfiguration` is deprecated and will be removed in 6.0. Inject the required parameters instead.

NodeBundle
----------

 * `CronUpdateNodeCommand::__construct()` now takes an instance of `Doctrine\ORM\EntityManagerInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `InitAclCommand::__construct()` now takes an instance of `Doctrine\ORM\EntityManagerInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `CronUpdateNodeCommand` and `InitAclCommand` have been marked as final.
 * `Possibility to change the icon of your page.`: The possibility already exists to change the icon in the sidebar tree of your page. This was already available by yml configuration. I've added a new interface, TreeIconInterface that can be implemented and that will return the icon that should be used.
 * The unused `WidgetsController::selectNodesLazySearch` action is deprecated and will be removed in 6.0. 
 * Injecting the container in the `SlugRouter` is deprecated and will be removed in 6.0. Inject the required services instead.
 * The `service` method of `Kunstmaan\NodeBundle\Entity\PageInterface` is deprecated and will be removed in 6.0. Implement the `Kunstmaan\NodeBundle\Controller\SlugActionInterface`
   and use the `getControllerAction` method to specify a controller action with your custom page logic instead. 


SearchBundle
------------

 * `SetupIndexCommand::__construct()`, `PopulateIndexCommand::__construct()` and `DeleteIndexCommand::__construct()` now takes an instance of `Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `SetupIndexCommand`, `PopulateIndexCommand` and `DeleteIndexCommand` have been marked as final.
 

TranslatorBundle
----------------

 * `ExportTranslationsCommand::__construct()` now takes an instance of `Kunstmaan\TranslatorBundle\Service\Command\Exporter\ExportCommandHandler` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `ImportTranslationsCommand::__construct()` now takes an instance of `Kunstmaan\TranslatorBundle\Service\Command\Importer\ImportCommandHandler` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `TranslationCacheCommand::__construct()` now takes an instance of `Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `TranslationFlagCommand::__construct()` now takes an instance of `Kunstmaan\TranslatorBundle\Repository\TranslationRepository` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `ExportTranslationsCommand`, `ImportTranslationsCommand`, `MigrationsDiffCommand`, `TranslationCacheCommand` and `TranslationFlagCommand` have been marked as final.

UtilitiesBundle
---------------

 * `CipherCommand::__construct()` now takes an instance of `Kunstmaan\UtilitiesBundle\Helper\Cipher\CipherInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `CipherCommand` has been marked as final.
