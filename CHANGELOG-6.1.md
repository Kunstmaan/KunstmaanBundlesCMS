CHANGELOG for 6.1.x
===================

This changelog references the relevant changes (bug and security fixes) done in 6.1 minor versions.

To get the diff for a specific change, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/compare/6.1.0...6.1.1

## 6.1.3 / 2022-01-28

* [LeadGenerationBundle] Add missing controller services [#3060](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3060) ([@acrobat](https://github.com/acrobat))
* [LeadGenerationBundle] Allow symfony/deprecation-contracts v3 [#3049](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3049) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Only apply login subscriber to users of cms admin [#3069](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3069) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Fix php8 incompatibility in article generator repository [#3057](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3057) ([@acrobat](https://github.com/acrobat))
* [Dashboardbundle] Make ConfigHelper service lazy to avoid constructor queries [#3056](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3056) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix test incompatibility with doctrine/orm 2.11.0 [#3047](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3047) ([@acrobat](https://github.com/acrobat))

## 6.1.2 / 2022-01-10

* [AdminBundle] Remove self-deprecation in tests and add missing deprecation test [#3034](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3034) ([@acrobat](https://github.com/acrobat))
* [RedirectBundle] Fix redirect route controller notation [#3042](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3042) ([@acrobat](https://github.com/acrobat))
* [CookieBundle] Add missing view data provider service [#3039](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3039) ([@acrobat](https://github.com/acrobat))
* [UtilitiesBundle] Fix deprecated urltransationnamingstrategy [#3036](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3036) ([@Numkil](https://github.com/Numkil))
* [MultiDomainBundle] fix bug in service definition [#3030](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3030) ([@Numkil](https://github.com/Numkil))
* [NodeBundle] Improve performance of NodeMenu class [#3046](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3046) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Escape user input to avoid xss issues [#3038](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3038) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Upgrade phpstan to stable release [#3037](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3037) ([@acrobat](https://github.com/acrobat))

## 6.1.1 / 2021-11-9

* [MultiDomainBundle] fix bug in service definition [#3030](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3030) ([@Numkil](https://github.com/Numkil))

## 6.1.0 / 2021-11-9

* [CookieBundle] Remove remaining sf3 bc layers [#3025](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3025) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix sf5.4 AbstractController deprecations [#3019](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3019) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix sf 5.4 deprecations and simplify translator test config [#3018](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3018) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix some undefined classes warnings [#3013](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3013) ([@acrobat](https://github.com/acrobat))
* [AllBundle] PHP 8.1 compatibility fixes [#3012](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3012) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Restore deprecation helper to not allow self deprecations [#3009](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3009) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Deprecate new_authentication enable config [#3007](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3007) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Replace deprecated symfony/debug package [#3006](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3006) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Sf 3.4 and elastica 6 bc layer cleanup [#3005](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3005) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove symfony event sf3.4 compatibility layer [#2998](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2998) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove legacy symfony 3.4 eventdispatcher check [#2995](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2995) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix twig syntax error [#2994](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2994) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix symfony 5 deprecations on username method [#2993](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2993) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Cleanup of symfony 3.4 compatibility layers [#2991](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2991) ([@acrobat](https://github.com/acrobat))
* [MultiDomainBundle] host override cleanup compatiblity with new authentication system [#2989](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2989) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Multiple sf5 deprecations fixes [#2986](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2986) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix sf5 deprecations after upmerge [#2985](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2985) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Sf3 compatibility layer cleanup [#2967](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2967) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Allow symfony 5 and compatibility fixes [#2964](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2964) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove symfony 3.4 support [#2963](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2963) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Rework controller for symfony 5 compatibility [#2962](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2962) ([@acrobat](https://github.com/acrobat))
