CHANGELOG for 7.2.x
===================

This changelog references the relevant changes (bug and security fixes) done in 7.2 minor versions.

To get the diff for a specific change, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/compare/7.2.0...7.2.1

## 7.2.0 / 2025-01-31

* [GeneratorBundle] Add missing upgrade doc for generator bundle [#3469](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3469) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Mark all generator classes as internal [#3465](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3465) ([@acrobat](https://github.com/acrobat))
* [FormBundle] Forward compatibility fix for symfony 7.2 in tests [#3464](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3464) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix fixture deprecations [#3459](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3459) ([@dannyvw](https://github.com/dannyvw))
* [MediaPagePartBundle] Add missing notblank constraint to media pageparts [#3456](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3456) ([@dannyvw](https://github.com/dannyvw))
* [RedirectBundle] Add support for redirect origin wildcard, excluding root path [#3454](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3454) ([@dannyvw](https://github.com/dannyvw))
* [PagePart] (fix) skip checking for not persisted sub entites delete c… [#3453](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3453) ([@mart-insiders](https://github.com/mart-insiders))
* [NodeBundle] Remove unnecessary count calls [#3448](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3448) ([@dannyvw](https://github.com/dannyvw))
* [MultidomainBundle] Improve domain based locale router performance [#3444](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3444) ([@dannyvw](https://github.com/dannyvw))
* [NodeBundle] Fix not found nodetranslation and media in urlhelper [#3442](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3442) ([@dannyvw](https://github.com/dannyvw))
* [FormBundle] Fix form submissions export icons [#3438](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3438) ([@dannyvw](https://github.com/dannyvw))
* [UtilitiesBundle] Fix instanceof proxy [#3435](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3435) ([@dannyvw](https://github.com/dannyvw))
* [NodeBundle] Remove not needed request_stack [#3434](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3434) ([@dannyvw](https://github.com/dannyvw))
* [NodeBundle] Dispatch ADD_NODE event on duplicate page [#3433](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3433) ([@dannyvw](https://github.com/dannyvw))
* [NodeBundle] Refactor Url chooser without XHR call [#3432](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3432) ([@mart-insiders](https://github.com/mart-insiders))
* [MediaBundle] Media Folder include subdirectories filter [#3430](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3430) ([@mart-insiders](https://github.com/mart-insiders))
* [GeneratorBundle] Fix form pagepart view variable [#3429](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3429) ([@dannyvw](https://github.com/dannyvw))
* [MultidomainBundle] Check of extra data key is defined [#3427](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3427) ([@dannyvw](https://github.com/dannyvw))
* [RedirectBundle] Increase redirect origin field length [#3424](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3424) ([@dannyvw](https://github.com/dannyvw))
* [AdminBundle] Menu order [#3417](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3417) ([@dannyvw](https://github.com/dannyvw))
* [AllBundles] Improve tests [#3412](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3412) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Symfony 7 forward compatibility fixes [#3411](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3411) ([@acrobat](https://github.com/acrobat))
* [FormBundle] Fix form pagepart view variable with symfony template attribute [#3410](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3410) ([@acrobat](https://github.com/acrobat))
* [AllBundles] More fixes of deprecations in doctrine event listeners [#3409](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3409) ([@acrobat](https://github.com/acrobat))
* [PagePartBundle] Add page to PagePartEvent [#3407](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3407) ([@dannyvw](https://github.com/dannyvw))
* [NodeBundle] Fix incorrect event type in doctrine listener [#3400](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3400) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Add missing autowire service alias [#3399](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3399) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix monolog-bridge logger deprecation [#3398](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3398) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Refactor fixtures to dependency injection [#3397](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3397) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Update encore package.json for webpack-encore-bundle 2 [#3390](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3390) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Prepend gedmo translatable doctrine mapping with attributes [#3389](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3389) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix doctrine related deprecations [#3388](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3388) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Set correct routing yaml attribute type [#3387](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3387) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Drop Symfony 5.4 support [#3381](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3381) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Replace sensio/framework-extra-bundle with built-in symfony attributes [#3378](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3378) ([@acrobat](https://github.com/acrobat)) 
* [TranslatorBundle] Backport fixtures load deprecation fix [#3467](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3467) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Upgrade phpstan to 2.0 [#3466](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3466) ([@acrobat](https://github.com/acrobat))
