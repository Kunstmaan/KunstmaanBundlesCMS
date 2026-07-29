CHANGELOG for 7.3.x
===================

This changelog references the relevant changes (bug and security fixes) done in 7.3 minor versions.

To get the diff for a specific change, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/compare/7.3.0...7.3.1

## 7.3.1 / 2026-07-27

* [FormBundle] Security fix [GHSA-j376-w3x3-q674](https://github.com/advisories/GHSA-j376-w3x3-q674) [3c25383](https://github.com/Kunstmaan/KunstmaanBundlesCMS/commit/3c253839429739bab01d9732cbc206534cfbdc7a) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Backport Symfony 7.4 deprecation fixes [#3535](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3535) ([@acrobat](https://github.com/acrobat))

## 7.3.0 / 2025-02-16

* [AllBundles] Add php 8.4 to CI workflow [#3498](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3498) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Fix leftover closing tag of spaceless removal [#3497](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3497) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove deprecated twig spaceless tag [#3496](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3496) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Fix fixture load return type deprecation [#3495](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3495) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Various dependency deprecation fixes [#3493](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3493) ([@acrobat](https://github.com/acrobat))
* [AllBundle] PHP 8.4 deprecation fixes [#3492](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3492) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Symfony 7 compatibility fixes [#3491](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3491) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Add correct return type for Symfony 7 support [#3489](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3489) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix pagerfanta dependencies for v4 support [#3488](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3488) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Update remaining packages for Symfony 7 support [#3487](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3487) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix symfony 7.3 deprecations [#3485](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3485) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Allow symfony 7 [#3484](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3484) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix compatibility with doctrine fixtures 2.0 [#3478](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3478) ([@dannyvw](https://github.com/dannyvw))
* [MultiDomainBundle] Prevent duplicate calls for getting node translation [#3477](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3477) ([@dannyvw](https://github.com/dannyvw))
* [AdminBundle] Remove session security listener if values are not enabled [#3475](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3475) ([@dannyvw](https://github.com/dannyvw))
* [RedirectBundle] Redirect with spaces [#3471](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3471) ([@dannyvw](https://github.com/dannyvw))
* [TranslatorBundle] Add Symfony 7 forward compatibility [#3470](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3470) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Remove kunstmaan/sensio-generator-bundle dependency [#3468](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3468) ([@acrobat](https://github.com/acrobat)) 
* [GeneratorBundle] Bugfix incorrect form type class in article generator [#3494](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3494) ([@acrobat](https://github.com/acrobat))
