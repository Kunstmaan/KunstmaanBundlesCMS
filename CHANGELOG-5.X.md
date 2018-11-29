# Changelog

## 5.0.12 / 2018-11-29

* [AllBundles] Changes for upcoming travis-ci infra migration [#2180](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2180) ([@acrobat](https://github.com/acrobat))
* [Docs] Fix slack channel link [#2178](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2178) ([@diskwriter](https://github.com/diskwriter))
* [AllBundles] Backport styleci fixes to 5.0 [#2175](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2175) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix icons ckeditor again [#2174](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2174) ([@dbeerten](https://github.com/dbeerten))
* [GeneratorBundle] fix gulp dependency [#2172](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2172) ([@diskwriter](https://github.com/diskwriter))
* [GeneratorBundle] Remove unnecessary roles on dom nodes [#2170](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2170) ([@diskwriter](https://github.com/diskwriter))
* [AdminListBundle] use datepicker for DateTimeType filters [#2159](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2159) ([@dbeerten](https://github.com/dbeerten))
* [TranslatorBundle] fix non-unique locales in TranslationAdminConfigurator [#2156](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2156) ([@treeleaf](https://github.com/treeleaf))
* [AllBundles] Disable styleci checks for 5.0 [#2151](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2151) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] revert CKeditor to original version [#2134](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2134) ([@dbeerten](https://github.com/dbeerten))
* [NodeBundle] Revert "Fix bug in NodeChoiceType (#2115)" [#2133](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2133) ([@deZinc](https://github.com/deZinc))

## 5.0.11 / 2018-09-12

* [NodeSearchBunde] [SearchBundle]  Add parameters for ES authentication and use them in version check [#2120](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2120) ([@wesleylancel](https://github.com/wesleylancel)) 
* [AdminBundle] use full version of CKeditor [#2119](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2119) ([@dbeerten](https://github.com/dbeerten)) 
* [FormBundle] Show full path to file in form export [#2118](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2118) ([@waaghals](https://github.com/waaghals)) 
* [NodeBundle] Node choice type fix [#2115](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2115) ([@wesleylancel](https://github.com/wesleylancel)) 
* [AdminBundle] Fixed issue with locale in kuma:user:create command [#2104](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2104) ([@veloxy](https://github.com/veloxy)) 
* [AdminBundle] Fixed issue when running kuma:user:create with --no-interaction flag [#2103](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2103) ([@veloxy](https://github.com/veloxy)) 
* [AdminBundle] Fix icons CKeditor [#2099](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2099) ([@dbeerten](https://github.com/dbeerten)) 
* [NodeSearchBundle] Fix search index of childpages of a structured node [#2091](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2091) ([@acrobat](https://github.com/acrobat)) 


## 5.0.10 / 2018-08-21

* [NodeSearchBundle] check if params exist while building container [#2078](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2078) ([@Devolicious](https://github.com/Devolicious)) 


## 5.0.9 / 2018-08-21

* [MediaBundle] add return to load function [#2076](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2076) ([@bakie](https://github.com/bakie)) 
* [KunstmaanNodeSearchBundle] use hosts in useVersion6 check [#2075](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2075) ([@bakie](https://github.com/bakie)) 
* [MenuBundle] Fix menu item sorting issue with multiple menus [#2072](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2072) ([@acrobat](https://github.com/acrobat)) 


## 5.0.8 / 2018-08-07

* [AdminList] Fix admin list empty url [#2071](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2071) ([@dannyvw](https://github.com/dannyvw)) 
* [NodeBundle] Remove unused vardumper use [#2067](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2067) ([@acrobat](https://github.com/acrobat)) 
* [MediaBundle] Skip test if ghostscript is not installed [#2066](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2066) ([@acrobat](https://github.com/acrobat)) 
* [AdminBundle] #2050 Fixed issue in refreshing ck editor (don't do a destroy if the … [#2064](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2064) ([@janb87](https://github.com/janb87)) 
* [AdminListBundle] fixed form options source in admin list controller [#2056](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2056) ([@mdxpl](https://github.com/mdxpl)) 
* [KunstmaanTranslatorBundle] export all translations, use correct locales [#2055](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2055) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] Fix xpath for publish modal in behat tests [#2052](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2052) ([@acrobat](https://github.com/acrobat)) 
* [AdminBundle] Remove id of nested form item containers [#2049](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2049) ([@SpadXIII](https://github.com/SpadXIII)) 
* [AdminBundle] fixed BC break in CreateUserCommand [#2043](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2043) ([@deZinc](https://github.com/deZinc)) 
* [AdminBundle] remove hardcopy of ckeditor [#2041](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2041) ([@FVKVN](https://github.com/FVKVN)) 
* [AdminBundle] added missing translations to main language [#2038](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2038) ([@Devolicious](https://github.com/Devolicious)) 
* [MediaBundle] Fixed BackgroundFilterLoader issue [#2028](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2028) ([@deZinc](https://github.com/deZinc)) 


## 5.0.7 / 2018-06-21

* [PagePartBundle] Refactor HeaderPagePart entity test [#2024](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2024) ([@Devolicious](https://github.com/Devolicious)) 
* [KunstmaanNodeSearchBundle] remove check for class [#2009](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2009) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminBundle] fixed create user command #1995 [#1996](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1996) ([@deZinc](https://github.com/deZinc)) 
* [Docs] set correct paths [#1990](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1990) ([@sandergo90](https://github.com/sandergo90)) 
* [KunstmaanAdminBundle] fix js for collections [#1989](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1989) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] Replace deprecated twig raw tag in scss file [#1987](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1987) ([@acrobat](https://github.com/acrobat)) 
* [NodeBundle] Select correct root node for the urlchooser in a multidomain setup [#1986](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1986) ([@acrobat](https://github.com/acrobat)) 
* [KunstmaanFormBundle] batchsize is not initialized [#1976](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1976) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] Fix unquoted parameter in routing generator [#1973](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1973) ([@acrobat](https://github.com/acrobat)) 
* [ConfigBundle] Set the correct configuration entity as active [#1964](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1964) ([@JZuidema](https://github.com/JZuidema)) 
* [FormBundle] Change form labels to required [#1962](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1962) ([@dannyvw](https://github.com/dannyvw)) 
* [AdminBundle] Messages are already translated by symfony [#1954](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1954) ([@acrobat](https://github.com/acrobat)) 
* [AdminBundle] Update frontend packages [#1946](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1946) ([@diskwriter](https://github.com/diskwriter)) 
* [SensioInsights] adjust sensiolabs config [#1945](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1945) ([@Devolicious](https://github.com/Devolicious)) 
* [KunstmaanUtilitiesBundle]: assert bundle is only installed in dev [#1942](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1942) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminBundle]: toolbar should check if logged in in main firewall [#1941](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1941) ([@sandergo90](https://github.com/sandergo90)) 
* [UtilitiesBundle] Make parameter kunstmaan_utilities.cipher.secret op… [#1939](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1939) ([@treeleaf](https://github.com/treeleaf)) 
* [FormBundle] Change label required for choice pagepart [#1934](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1934) ([@dannyvw](https://github.com/dannyvw)) 
* [FormBundle] Update regex translation [#1933](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1933) ([@dannyvw](https://github.com/dannyvw)) 
* [MenuBundle] Change new window required [#1932](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1932) ([@dannyvw](https://github.com/dannyvw)) 
* [AdminListBundle][MediaBundle] remove duplicate pagination and broken switch pagination limit [#1927](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1927) ([@Numkil](https://github.com/Numkil)) 
* [Composer][GeneratorBundle] Fixes for testing StandardEdition [#1922](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1922) ([@Devolicious](https://github.com/Devolicious)) 
* [AdminBundle] Update add new translation [#1914](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1914) ([@dannyvw](https://github.com/dannyvw)) 
* [Documentation] remove addition to upgrade guide that was merged in incorrectly [#1911](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1911) ([@Numkil](https://github.com/Numkil)) 
* [AdminListBundle] avoid translations freakout when the key is true or false without escaping [#1909](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1909) ([@Numkil](https://github.com/Numkil)) 
* [NodeBundle] save on (un)publish [#1803](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1803) ([@deZinc](https://github.com/deZinc)) 
* [MediaBundle] Create runtime config hash using the original image path [#1500](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1500) ([@b-franco](https://github.com/b-franco)) 


## 5.0.6 / 2018-04-13

* [GeneratorBundle] Fix article generator for no-interaction [#1912](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1912) ([@Devolicious](https://github.com/Devolicious)) 
* [NodeBundle]: node data collector should go to node id [#1891](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1891) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminListBundle] Start and end form using tab pane form view, if it exists [#1890](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1890) ([@mtnorthrop](https://github.com/mtnorthrop)) 
* [MultiDomainBundle]: host override should be set before getting default locale [#1889](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1889) ([@sandergo90](https://github.com/sandergo90)) 
* [FormBundle]: add batchsize to export list [#1887](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1887) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminBundle] Fix Bootstrap modals on smaller screens [#1885](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1885) ([@dbeerten](https://github.com/dbeerten)) 
* [Docs - 5.0]: move to readthedocs documentation [#1880](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1880) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] Change logic of page title [#1874](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1874) ([@treeleaf](https://github.com/treeleaf)) 
* [GeneratorBundle] Fixed limit in generated article repository [#1871](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1871) ([@jordanmoon](https://github.com/jordanmoon)) 
* [KunstmaanAdminBundle]: fix permissions [#1869](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1869) ([@sandergo90](https://github.com/sandergo90)) 
* [KunstmaanAdminBundle]: access level for google should add groups [#1868](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1868) ([@sandergo90](https://github.com/sandergo90)) 
* [LeadGenerationBundle]: times should not be blank [#1865](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1865) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] Fixed bundle namespace in article generator pagepart config [#1864](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1864) ([@jordanmoon](https://github.com/jordanmoon)) 
* [AdminBundle]: redirect to previous page when logging in with Google [#1862](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1862) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle]: fixes for article generator [#1860](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1860) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminBundle] Make it possible to search in all PagePartChoosers [#1854](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1854) ([@NindroidX](https://github.com/NindroidX)) 
* [SeoBundle]: fields are not required [#1851](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1851) ([@sandergo90](https://github.com/sandergo90)) 
* [NodeBundle] Check for mapping exception when multiple entity manager… [#1819](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1819) ([@delboy1978uk](https://github.com/delboy1978uk)) 


## 5.0.5 / 2018-03-05

* [AdminBundle] Fix escaping of img src attributes in WYSIWYG fields [#1858](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1858) ([@mtnorthrop](https://github.com/mtnorthrop)) 


## 5.0.4 / 2018-02-20

* [AdminListBundle]: ->loadTemplate() is only for internal use.. [#1846](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1846) ([@sandergo90](https://github.com/sandergo90)) 
* [MediaBundle] Bulk file upload fix [#1834](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1834) ([@cv65kr](https://github.com/cv65kr)) 
* [AdminBundle]: re-add the extrajavascript block [#1829](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1829) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminBundle] refactor media token transformer [#1827](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1827) ([@delboy1978uk](https://github.com/delboy1978uk)) 
* [AdminListBundle] Fix Export Exception namespace [#1825](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1825) ([@delboy1978uk](https://github.com/delboy1978uk)) 
* [Leadgenerationbundle]: abstract functions should be public [#1817](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1817) ([@sandergo90](https://github.com/sandergo90)) 
* [KunstmaanNodeBundle] admin styles no longer have .min extension [#1813](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1813) ([@sandergo90](https://github.com/sandergo90)) 


## 5.0.3 / 2018-02-06

* [KunstmaanAdminBundle] change php7 to php5 code [#1807](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1807) ([@sandergo90](https://github.com/sandergo90)) 


## 5.0.2 / 2018-02-05

* [KunstmaanAdminBundle]: rebuild styling [#1806](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1806) ([@sandergo90](https://github.com/sandergo90)) 


## 5.0.1 / 2018-02-02

* [All]: Bump version for upmerged bugfix 


## 5.0.0 / 2018-01-31

* [ArticleBundle]: set correct translation [#1801](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1801) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] twig files should use the generated article class name [#1800](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1800) ([@sandergo90](https://github.com/sandergo90)) 
* [KunstmaanNodeSearchBundle] add keyword as data type [#1793](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1793) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminBundle] add option to specify provider keys for toolbar listener [#1787](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1787) ([@sandergo90](https://github.com/sandergo90)) 
* [DashboardBundle] fix javascript error [#1786](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1786) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] Update babel env [#1755](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1755) ([@dbeerten](https://github.com/dbeerten)) 

## 5.0.0-RC2 / 2018-01-02

* [NodeBundle]: better node translation listener [#1772](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1772) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminBundle][Improvment] Exclude certain stuff from the exception list [#1765](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1765) ([@cv65kr](https://github.com/cv65kr)) 
* [FormBundle] removed discriminator map from submission field [#1764](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1764) ([@Devolicious](https://github.com/Devolicious)) 
* [AdminBundle][Feature] Resolve all exceptions [#1763](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1763) ([@cv65kr](https://github.com/cv65kr)) 
* [UtilitiesBundle] Depracted arguments [#1753](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1753) ([@cv65kr](https://github.com/cv65kr)) 
* [GeneratorBundle] Refactor frontend JS [#1752](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1752) ([@dbeerten](https://github.com/dbeerten)) 
* [AllBundles] Upgrade docs for deprecated form types [#1749](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1749) ([@delboy1978uk](https://github.com/delboy1978uk)) 
* [Docs] Add liipimagine changes to upgrade guide [#1743](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1743) ([@Devolicious](https://github.com/Devolicious)) 
* [AllBundles] Set minimum symfony version to 3.4 [#1741](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1741) ([@treeleaf](https://github.com/treeleaf)) 
* [AdminListBundle] add ods in requirements format since its now supported [#1738](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1738) ([@Numkil](https://github.com/Numkil)) 
* [TranslatorBundle] Use correct parameter to define locale columns [#1724](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1724) ([@wesleylancel](https://github.com/wesleylancel)) 
* [AdminBundle] Fix toolbar version checker [#1721](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1721) ([@cv65kr](https://github.com/cv65kr)) 
* [SearchBundle][FIX] SetupIndex command failing [#1708](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1708) ([@treeleaf](https://github.com/treeleaf)) 
* [SearchBundle] Convert analyzer languages config to lowercase … [#1706](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1706) ([@Numkil](https://github.com/Numkil)) 
* [SearchBundle][FIX] Populate command failing  [#1705](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1705) ([@cv65kr](https://github.com/cv65kr)) 
* [NodeBundle]: remove request stack injection [#1703](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1703) ([@sandergo90](https://github.com/sandergo90)) 
* [SensiolabsInsight] Removed unused constructor parameter [#1699](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1699) ([@acrobat](https://github.com/acrobat)) 
* [SensiolabsInsight] Removed undefined implemented class [#1698](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1698) ([@acrobat](https://github.com/acrobat)) 
* [AllBundles][5.0] Fix JavaScript loading errors [#1693](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1693) ([@NindroidX](https://github.com/NindroidX)) 
* [NodeBundle] unused class property [#1684](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1684) ([@delboy1978uk](https://github.com/delboy1978uk)) 
* [SensiolabsInsight] Throw AccessDeniedException instead of AccessDeniedHttpException [#1674](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1674) ([@acrobat](https://github.com/acrobat)) 
* [UtilitiesBundle] Cipher deprecations [#1673](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1673) ([@cv65kr](https://github.com/cv65kr)) 
* [UtilitiesBundle] Feature transliterator [#1666](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1666) ([@cv65kr](https://github.com/cv65kr)) 
* [GeneratorBundle] update default stylelint [#1665](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1665) ([@dbeerten](https://github.com/dbeerten)) 
* [GeneratorBundle] Separate import rules for scss imports [#1664](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1664) ([@dbeerten](https://github.com/dbeerten)) 
* [AdminBundle] Exceptions list [#1637](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1637) ([@cv65kr](https://github.com/cv65kr)) 
* [NodeSearchBundle][SearchBundle] Multi domain/language search population fix [#1635](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1635) ([@cv65kr](https://github.com/cv65kr)) 
* [MediaBundle] Filter in Media Thumbnail View [#1627](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1627) ([@delboy1978uk](https://github.com/delboy1978uk)) 
* [Toolbar]: return empty array when no data can be collected [#1618](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1618) ([@sandergo90](https://github.com/sandergo90)) 
* [AdminBundle] Fix the extended ppchooser search for IE11 [#1616](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1616) ([@NindroidX](https://github.com/NindroidX)) 
* [UI] Split polyfills from the bundle JavaScript code [#1615](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1615) ([@NindroidX](https://github.com/NindroidX)) 
* [AdminBundle] Fix the ppchooser preview placeholder for Firefox [#1614](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1614) ([@NindroidX](https://github.com/NindroidX)) 
* [AllBundles]: fix toolbar when profiler is not enabled [#1613](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1613) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] Use livingcss to build styleguide (remove hologram & gems) [#1612](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1612) ([@dbeerten](https://github.com/dbeerten)) 
* [AdminBundle]: check if profiler is enabled [#1611](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1611) ([@sandergo90](https://github.com/sandergo90)) 
* [GeneratorBundle] Article generator fix [#1608](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1608) ([@cv65kr](https://github.com/cv65kr)) 
* [PagePartBundle] Optimize the extended Pagepart chooser [#1600](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1600) ([@NindroidX](https://github.com/NindroidX)) 
* [PagePartBundle] fix pp chooser button after add [#1598](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1598) ([@Devolicious](https://github.com/Devolicious)) 
* [GeneratorBundle] refactor to new SF3 forms with FQCN [#1597](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1597) ([@NindroidX](https://github.com/NindroidX)) 
* [GeneratorBundle] fix/refactor forms fqcn [#1595](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1595) ([@deZinc](https://github.com/deZinc)) 
* [GeneratorBundle] Replace all rem values with px [#1593](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1593) ([@NindroidX](https://github.com/NindroidX)) 
* [AdminListBundle] Fix check canAdd on pages adminlist [#1591](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1591) ([@Devolicious](https://github.com/Devolicious)) 
* [NodeSearchBundle] fixes for elasticsearch 5.0 [#1589](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1589) ([@Devolicious](https://github.com/Devolicious)) 
* [UI] Added some documentation on how to contribute to the Ground Control skeleton [#1583](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1583) ([@janb87](https://github.com/janb87)) 
* [DashboardBundle] Fix missing setup scripts for GA dashboard [#1582](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1582) ([@Devolicious](https://github.com/Devolicious)) 
* [UI] Extracted webpack configuration into separate files [#1570](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1570) ([@janb87](https://github.com/janb87)) 
* [AdminListBundle] refactor and modernize exportservice [#1556](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1556) ([@Numkil](https://github.com/Numkil)) 
* [VotingBundle] Remove duplicate code in VotingBundle [#1406](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1406) ([@dannyvw](https://github.com/dannyvw)) 
* [AllBundles]: new custom data collectors and custom toolbar [#1274](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1274) ([@sandergo90](https://github.com/sandergo90)) 


## 5.0.0-RC1 / 2017-08-01

* [MenuBundle] Fix adminlist to use FQCN instead of object for form type [#1564](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1564) ([@Devolicious](https://github.com/Devolicious)) 
* [UI] Added buildGroundControlSkeleton task [#1560](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1560) ([@janb87](https://github.com/janb87)) 
* [SeoBundle] Fix Seo tab nodelistener [#1559](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1559) ([@Devolicious](https://github.com/Devolicious)) 
* [UI] Added server & watch tasks for development [#1548](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1548) ([@dbeerten](https://github.com/dbeerten)) 
* [AdminBundle] [GeneratorBundle]: Add possibility to add extra js for the backend [#1539](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1539) ([@dbeerten](https://github.com/dbeerten)) 
* [DOCS]: add gulp-cli dependency [#1533](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1533) ([@dbeerten](https://github.com/dbeerten)) 
* [Composer] update fos userbundle dependency [#1531](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1531) ([@Devolicious](https://github.com/Devolicious)) 
* [AllBundles] Improved FE development environment [#1528](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1528) ([@janb87](https://github.com/janb87)) 
* [AdminBundle] Remove key for ConsoleExceptionListener argument [#1516](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1516) ([@wesleylancel](https://github.com/wesleylancel)) 
* [ALL] use gulp for cms assets & add compiled assets to public folders [#1510](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1510) ([@dbeerten](https://github.com/dbeerten)) 
* [KunstmaanNodeSearchBundle]: upgrade elastica to 5.1 [#1494](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1494) ([@sandergo90](https://github.com/sandergo90)) 
* [All] move from php 5.5 to php 5.6 [#1471](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1471) ([@Devolicious](https://github.com/Devolicious)) 
* [All] fix cmf-routing dependency [#1468](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1468) ([@Devolicious](https://github.com/Devolicious)) 
* [NodeSearchBundle] Make NodeSearcher properties protected for extending [#1451](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1451) ([@b-franco](https://github.com/b-franco)) 
* [NodeBundle] Remove default for is_homepage [#1442](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1442) ([@wesleylancel](https://github.com/wesleylancel)) 
* [ArticleBundle] [GeneratorBundle] Add support for tags and categories in ArticleBundle and refactoring of article generator [#1391](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1391) ([@treeleaf](https://github.com/treeleaf)) 
* [AllBundles][BC break] Change initiating new form type by fqcn (SF3 style) [#1348](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1348) ([@aistis-](https://github.com/aistis-)) 
