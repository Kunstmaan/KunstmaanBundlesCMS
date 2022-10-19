CHANGELOG for 6.1.x
===================

This changelog references the relevant changes (bug and security fixes) done in 6.1 minor versions.

To get the diff for a specific change, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/compare/6.1.0...6.1.1

## 6.1.7 / 2022-10-19

* [AllBundles] Enable dependabot to update the used github action versions [#3186](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3186) ([@acrobat](https://github.com/acrobat))
* [Dashboardbundle] Explicit int cast to avoid php 8.1 warning [#3181](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3181) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix current request check for forwarded error page requests [#3178](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3178) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Use stable version of subtree splitter and don't limit on path to allow splitting of newly pushed branches [#3174](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3174) ([@acrobat](https://github.com/acrobat))

## 6.1.6 / 2022-10-05

* [NodeBundle] Temporary disable csrf check on delete because conflict with other page types [#3153](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3153) ([@acrobat](https://github.com/acrobat))
* [AllBiundles] Update demo site url [#3168](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3168) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Update cms website urls to new domain + update doc urls [#3161](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3161) ([@acrobat](https://github.com/acrobat))
* [RedirectBundle] Save empty domain so redirect router catches all incoming domains for project [#3160](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3160) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Update setup-node action to latest version [#3151](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3151) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] Fix sqlite error in translator tests with doctrine/dbal v2.10+ [#3150](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3150) ([@acrobat](https://github.com/acrobat))

## 6.1.5 / 2022-06-26

* [NodeBundle] issue where max weight could be exceeded and cause irregular behaviour with sorting [#3143](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3143) ([@Numkil](https://github.com/Numkil))
* [AdminBundle] Limit doctrine/persistence to 2.x to avoid issues with deprecated entity notation [#3142](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3142) ([@acrobat](https://github.com/acrobat))

## 6.1.4 / 2022-06-07

* [FormBundle] Fix label docblock [#3132](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3132) ([@dannyvw](https://github.com/dannyvw))
* [NodeBundle] Implement controller improvements for restored node search action [#3128](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3128) ([@acrobat](https://github.com/acrobat))
* [MultidomainBundle] Fix eventsubscriber service config [#3118](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3118) ([@acrobat](https://github.com/acrobat))
* [DashboardBundle] Fix trying to access array offset on value of type null [#3109](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3109) ([@dannyvw](https://github.com/dannyvw))
* [UserManagementBundle] Fix user events [#3107](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3107) ([@dannyvw](https://github.com/dannyvw))
* [NodeBundle] Fix missing route bc-break in url chooser search [#3127](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3127) ([@acrobat](https://github.com/acrobat))
* [CookieBundle] Fix php8 deprecation for passing null to stripos [#3106](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3106) ([@acrobat](https://github.com/acrobat))
* [UserManagementBundle] Restore user edit/delete initialize events [#3099](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3099) ([@acrobat](https://github.com/acrobat))
* [MultidomainBundle] Restore slug router parameter override [#3098](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3098) ([@acrobat](https://github.com/acrobat))
* [MediaPagePartBundle] Fix nullable url [#3135](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3135) ([@dannyvw](https://github.com/dannyvw))
* [SeoBundle] Revert espacing extra metatags string to avoid render issue [#3133](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3133) ([@acrobat](https://github.com/acrobat))

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
