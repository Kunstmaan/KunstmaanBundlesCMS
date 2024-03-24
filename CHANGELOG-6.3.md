CHANGELOG for 6.3.x
===================

This changelog references the relevant changes (bug and security fixes) done in 6.3 minor versions.

To get the diff for a specific change, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/compare/6.3.0...6.3.1

## 6.3.1 / 2024-03-24

* [AllBundles] Fix php-cs-fixer issues [#3353](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3353) ([@acrobat](https://github.com/acrobat))
* [AdminListBundle] Fix doctrine related unit tests [#3345](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3345) ([@acrobat](https://github.com/acrobat))
* Apply nullable_type_declaration_for_default_null_value and skip fully_qualified_strict_types [#3332](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3332) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Symfony 6 compatibility fixes [#3322](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3322) ([@acrobat](https://github.com/acrobat))

## 6.3.0 / 2023-12-20

* [CookieBundle] Deprecate entity controller action method [#3297](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3297) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Removed unused egulias/email-validator dependency [#3296](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3296) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Enable CI testing on php 8.2 [#3294](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3294) ([@acrobat](https://github.com/acrobat))
* [UtilitiesBundle] Replace deprecated doctrine proxy interface [#3293](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3293) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Bump twig dependency and replace abandoned twig/extensions package [#3285](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3285) ([@acrobat](https://github.com/acrobat))
* [PagePartBundle] Fix parameter bag retriaval of array params [#3284](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3284) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Symfony 6 compatibility fixes [#3281](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3281) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Bump sensio/framework-extra-bundle to ^6.0 [#3280](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3280) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Improve php/phpdoc return types [#3279](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3279) ([@acrobat](https://github.com/acrobat))
* [FormBundle] Replace container access with dependency injection in FormHandler [#3276](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3276) ([@acrobat](https://github.com/acrobat))
* [MultidomainBundle] Deprecation logout handler [#3275](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3275) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Add missing phpdoc return types on dependency injection classes [#3274](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3274) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Improve phpdoc and real return types [#3270](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3270) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] TabPane can be nullable [#3269](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3269) ([@dannyvw](https://github.com/dannyvw))
* [AdminBundle] trim(null) is not allowed [#3267](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3267) ([@tarjei](https://github.com/tarjei))
* [AdminBundle] Fix security exception deprecation [#3251](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3251) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Fix symfony 6 errorNames property deprecations [#3250](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3250) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Add correct phpdoc return types for upcoming symfony 6 support [#3249](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3249) ([@acrobat](https://github.com/acrobat))
* [UtilitiesBundle] Change docblock for slugifier [#3243](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3243) ([@dannyvw](https://github.com/dannyvw))
* [MediaPagePartBundle] Add admin view for media pageparts [#3242](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3242) ([@dannyvw](https://github.com/dannyvw))
* [RedirectBundle] Improve performance of the redirect router [#3239](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3239) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Only get soundcloud thumbnail if api key is available [#3237](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3237) ([@dannyvw](https://github.com/dannyvw))
* [AdminBundle] Add support for form help text in fields [#3216](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3216) ([@acrobat](https://github.com/acrobat))
* [UtilitiesBundle] Deprecate custom new relic naming strategy [#3215](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3215) ([@acrobat](https://github.com/acrobat))
* [AllBundles] General fixes [#3213](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3213) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Move command defintion to AsCommand attribute to prepare symfony 6 support [#3211](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3211) ([@acrobat](https://github.com/acrobat))
* [MenuBundle] Add validation constraints [#3209](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3209) ([@dannyvw](https://github.com/dannyvw))
* [FixturesBundle] Replace deprecated symfony/inflector component [#3208](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3208) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Deprecate swiftmailer dependency [#3200](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3200) ([@acrobat](https://github.com/acrobat))
* [NodeBundle] Register gemdo tree listener to let bundle work without media bundle [#3199](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3199) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Replace old style doctrine entity notation by fqcn [#3180](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3180) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Make other cms bundles optional [#3179](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3179) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Enable automatic password upgrades by symfony security/hashers [#3177](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3177) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Drop symfony 4 support and remove BC layers [#3176](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3176) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Improve return types in generated files [#3175](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3175) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] AsMenuAdaptor attribute to easily define menu adaptors with custom priorities [#3173](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3173) ([@acrobat](https://github.com/acrobat))
* [AdminlistBundle][TranslatorBundle] Replace deprecated box/spout with openspout/openspout library [#3155](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3155) ([@Numkil](https://github.com/Numkil)) 
