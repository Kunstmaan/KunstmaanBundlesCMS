CHANGELOG for 6.0.x
===================

This changelog references the relevant changes (bug and security fixes) done in 6.0 minor versions.

To get the diff for a specific change, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/compare/6.0.0...6.0.1

## 6.0.6 / 2022-10-05

* [NodeBundle] Temporary disable csrf check on delete because conflict with other page types [#3153](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3153) ([@acrobat](https://github.com/acrobat))
* [AllBiundles] Update demo site url [#3168](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3168) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Update cms website urls to new domain + update doc urls [#3161](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3161) ([@acrobat](https://github.com/acrobat))
* [RedirectBundle] Save empty domain so redirect router catches all incoming domains for project [#3160](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3160) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Update setup-node action to latest version [#3151](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3151) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] Fix sqlite error in translator tests with doctrine/dbal v2.10+ [#3150](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3150) ([@acrobat](https://github.com/acrobat))

## 6.0.5 / 2022-06-26

* [NodeBundle] issue where max weight could be exceeded and cause irregular behaviour with sorting [#3143](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3143) ([@Numkil](https://github.com/Numkil))
* [AdminBundle] Limit doctrine/persistence to 2.x to avoid issues with deprecated entity notation [#3142](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3142) ([@acrobat](https://github.com/acrobat))

## 6.0.4 / 2022-06-07

* [NodeBundle] Fix missing route bc-break in url chooser search [#3127](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3127) ([@acrobat](https://github.com/acrobat))
* [CookieBundle] Fix php8 deprecation for passing null to stripos [#3106](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3106) ([@acrobat](https://github.com/acrobat))
* [UserManagementBundle] Restore user edit/delete initialize events [#3099](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3099) ([@acrobat](https://github.com/acrobat))
* [MultidomainBundle] Restore slug router parameter override [#3098](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3098) ([@acrobat](https://github.com/acrobat))
* [MediaPagePartBundle] Fix nullable url [#3135](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3135) ([@dannyvw](https://github.com/dannyvw))
* [SeoBundle] Revert espacing extra metatags string to avoid render issue [#3133](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3133) ([@acrobat](https://github.com/acrobat))

## 6.0.3 / 2022-01-28

* [AdminBundle] Only apply login subscriber to users of cms admin [#3069](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3069) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Fix php8 incompatibility in article generator repository [#3057](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3057) ([@acrobat](https://github.com/acrobat))
* [Dashboardbundle] Make ConfigHelper service lazy to avoid constructor queries [#3056](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3056) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix test incompatibility with doctrine/orm 2.11.0 [#3047](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3047) ([@acrobat](https://github.com/acrobat))

## 6.0.2 / 2022-01-10

* [RedirectBundle] Fix redirect route controller notation [#3042](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3042) ([@acrobat](https://github.com/acrobat))
* [CookieBundle] Add missing view data provider service [#3039](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3039) ([@acrobat](https://github.com/acrobat))
* [UtilitiesBundle] Fix deprecated urltransationnamingstrategy [#3036](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3036) ([@Numkil](https://github.com/Numkil))
* [MultiDomainBundle] fix bug in service definition [#3030](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3030) ([@Numkil](https://github.com/Numkil))
* [NodeBundle] Improve performance of NodeMenu class [#3046](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3046) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Escape user input to avoid xss issues [#3038](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3038) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Upgrade phpstan to stable release [#3037](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3037) ([@acrobat](https://github.com/acrobat))

## 6.0.1 / 2021-11-9

* [MultiDomainBundle] fix bug in service definition [#3030](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3030) ([@Numkil](https://github.com/Numkil))

## 6.0.0 / 2021-11-8

* [AllBundles] Remove deprecated code [#3023](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3023) ([@acrobat](https://github.com/acrobat))
* [CookieBundle] Compatibility/bug fixes with newer symfony versions [#3022](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3022) ([@acrobat](https://github.com/acrobat))
* [CookieBundle] Fixed error with default config [#3021](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3021) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Cleanup csrf deprecations [#3017](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3017) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] Fix upmerge issue [#3015](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3015) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Cleanup v5 upgrade/changelog files [#3014](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3014) ([@acrobat](https://github.com/acrobat))
* [NodeBundle] Update typehints to prepare php 8.1 support [#3011](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3011) ([@acrobat](https://github.com/acrobat))
* [MultiDomainBundle] Remove hostoverride listener deprecations [#3008](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3008) ([@acrobat](https://github.com/acrobat))
* [MultiDomainBundle] Fix failing test [#3001](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3001) ([@acrobat](https://github.com/acrobat))
* [CookieBundle] Move cookie-bundle source to monorepo [#2999](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2999) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Remove unused constructor argument in createUserCommand [#2997](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2997) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Major version cleanup [#2992](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2992) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove deprecated code [#2982](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2982) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove @final annotations [#2971](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2971) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Prepare code for 6.1 [#2970](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2970) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] Remaining translator cleanup [#2969](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2969) ([@acrobat](https://github.com/acrobat))
* [AllBundles] More remaining next major clean up [#2966](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2966) ([@acrobat](https://github.com/acrobat))
* [NodeBundkle] Remove service config for removed class [#2961](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2961) ([@acrobat](https://github.com/acrobat))
* [UserManagementBundle] Add missing final keyword to controller [#2958](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2958) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove controller deprecations [#2955](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2955) ([@acrobat](https://github.com/acrobat))
* [NodeSearchBundle] Remove deprecated code [#2952](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2952) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove slugAction deprecations [#2951](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2951) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Remove fos/user-bundle dependency and related code [#2941](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2941) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Remove unused imagine/imagine dependency [#2934](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2934) ([@acrobat](https://github.com/acrobat))
* [AllBundle] Fix command service definitions errors after cleanup [#2930](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2930) ([@acrobat](https://github.com/acrobat))
* [ConfigBundle] Remove deprecated code [#2929](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2929) ([@acrobat](https://github.com/acrobat))
* [DashboardBundle] Remove deprecated code [#2928](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2928) ([@acrobat](https://github.com/acrobat))
* [FormBundle] Remove deprecated code [#2927](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2927) ([@acrobat](https://github.com/acrobat))
* [FixturesBundle] Remove deprecated code [#2926](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2926) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Remove deprecated code [#2925](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2925) ([@acrobat](https://github.com/acrobat))
* [MultidomainBundle] Remove deprecated code [#2924](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2924) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Remove deprecated code [#2923](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2923) ([@acrobat](https://github.com/acrobat))
* [NodeBundle] Remove deprecated code [#2922](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2922) ([@acrobat](https://github.com/acrobat))
* [UtilitiesBundle] Remove deprecated code [#2921](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2921) ([@acrobat](https://github.com/acrobat))
* [UserManagementBundle] Remove deprecated code [#2920](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2920) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] Remove deprecated code [#2919](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2919) ([@acrobat](https://github.com/acrobat))
* [RedirectBundle] Remove deprecated code [#2918](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2918) ([@acrobat](https://github.com/acrobat))
* [ArticleBundle] Remove deprecated code [#2917](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2917) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Remove admin bundle deprecations [#2916](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2916) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove deprecated doctrine cache usages [#2915](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2915) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove deprecated container class parameters [#2914](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2914) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Remove deprecated code from version collector classes [#2913](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2913) ([@acrobat](https://github.com/acrobat))
* [UtilitiesBundle] Remove deprecated code [#2886](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2886) ([@acrobat](https://github.com/acrobat))
* [UserManagementBundle] Remove deprecated code [#2885](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2885) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] Remove deprecated code [#2884](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2884) ([@acrobat](https://github.com/acrobat))
* [NodeBundle] Remove deprecated code [#2883](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2883) ([@acrobat](https://github.com/acrobat))
* [MultiDomainBundle] Remove deprecated code [#2882](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2882) ([@acrobat](https://github.com/acrobat))
* [DashboardBundle] Remove deprecated code [#2881](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2881) ([@acrobat](https://github.com/acrobat))
* [ConfigBundle] Remove deprecated code [#2880](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2880) ([@acrobat](https://github.com/acrobat))
* [AdminListBundle] Remove deprecated code [#2879](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2879) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove deprecated ContainerAwareCommand usages [#2878](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2878) ([@acrobat](https://github.com/acrobat)) 
