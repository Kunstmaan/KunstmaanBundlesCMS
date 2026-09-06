CHANGELOG for 7.3.x
===================

This changelog references the relevant changes (bug and security fixes) done in 7.3 minor versions.

To get the diff for a specific change, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/compare/7.3.0...7.3.1

## 7.3.2 / 2026-09-06

* [MediaBundle] Compare the image mime type case insensitively [#3562](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3562) ([@acrobat](https://github.com/acrobat))
* [FormBundle] Remove duplicate image extensions from the upload allow list [#3561](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3561) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Fix image mime type check in the media validator [#3560](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3560) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Fix removal of empty media folders [#3559](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3559) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Harden the bulk upload file name handling [#3558](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3558) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Use phpstan symfony plugin [#3556](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3556) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix incorrect inline phpdoc notation and remove reduntant phpdoc typehints [#3555](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3555) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove outdated bc-layers [#3554](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3554) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix incorrect phdoc types [#3553](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3553) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix newly introduced codestyle issues [#3552](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3552) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Security fix [GHSA-p279-5wcv-45vq](https://github.com/Kunstmaan/KunstmaanBundlesCMS/security/advisories/GHSA-p279-5wcv-45vq) [182ca36](https://github.com/Kunstmaan/KunstmaanBundlesCMS/commit/182ca36de8714fc6192149b96a07255de54e285c) ([@acrobat](https://github.com/acrobat))

## 7.3.1 / 2026-07-27

* [FormBundle] Security fix [GHSA-j376-w3x3-q674](https://github.com/Kunstmaan/KunstmaanBundlesCMS/security/advisories/GHSA-j376-w3x3-q674) [3c25383](https://github.com/Kunstmaan/KunstmaanBundlesCMS/commit/3c253839429739bab01d9732cbc206534cfbdc7a) ([@acrobat](https://github.com/acrobat))
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
