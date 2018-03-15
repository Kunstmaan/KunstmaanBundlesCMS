UPGRADE FROM 5.0 to 5.1
=======================

## Deprecate use of old service id's

PR https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1814 introduces the use of FQCN as service id's instead of the old id's.
Therefore we added child definitions for the old service id's to still work.
When you are still using the old service id's in your project, you will get deprecation messages.
In KunstmaanBundlesCMS 6.0 the old service id's will be removed, so be sure to update your custom bundles.

AdminBundle
-----------

 * Passing the `logger` service as the second argument in `Kunstmaan\AdminBundle\Toolbar\BundleVersionDataCollector` is deprecated and will be removed in 6.0.

AdminListBundle
---------------

 * Getting services directly from the container in list controllers is deprecated and will be removed in 6.0. Register your controllers as services and inject the necessary dependencies.
 * Getting parameters directly from the container in list controllers is deprecated and will be removed in 6.0. Register your controllers as services and inject the necessary parameters.

ArticleBundle
-------------

 * Passing the `request_stack` service as the third argument in `Kunstmaan\ArticleBundle\Twig\ArticleTwigExtension` is deprecated and will be removed in 6.0.

ConfigBundle
------------

 * Passing the `container` as the sixth argument in `Kunstmaan\ConfigBundle\Controller\ConfigController` is deprecated in and will be removed in 6.0.

NodeBundle
----------

 * `CronUpdateNodeCommand::__construct()` now takes an instance of `Doctrine\ORM\EntityManagerInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `InitAclCommand::__construct()` now takes an instance of `Doctrine\ORM\EntityManagerInterface` as the first argument. Not passing it is deprecated and will throw a `TypeError` in 6.0.
 * `CronUpdateNodeCommand` and `InitAclCommand` have been marked as final.

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
