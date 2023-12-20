CHANGELOG for 6.2.x
===================

This changelog references the relevant changes (bug and security fixes) done in 6.2 minor versions.

To get the diff for a specific change, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/kunstmaan/KunstmaanBundlesCMS/compare/6.2.0...6.2.1

## 6.2.5 / 2023-12-20

* [AllBundles] Apply latest fixers of php-cs-fixer [#3287](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3287) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix orm attribute for user confirmation_token [#3266](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3266) ([@dannyvw](https://github.com/dannyvw))
* [NodeBundle] Throw not found exception if node is not found [#3265](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3265) ([@dannyvw](https://github.com/dannyvw))
* [GeneratorBundle] Only enable tty on platforms supporting it [#3260](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3260) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix PHP Deprecated:  trim(): Passing null to parameter #1 [#3254](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3254) ([@tarjei](https://github.com/tarjei))
* [GeneratorBundle] Bump bootstrap-sass which had .math calculation errors [#3253](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3253) ([@FVKVN](https://github.com/FVKVN))
* Bump actions/cache from 3.2.4 to 3.2.5 [#3252](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3252) ([@dependabot[bot]](https://github.com/apps/dependabot))

## 6.2.4 / 2023-02-09

* [AllBundles] Update default php-cs-fixer config for v3.14 [#3248](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3248) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] change in doctrine querybuilder on php8 systems caused our faulty expression to generate a non working query [#3236](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3236) ([@Numkil](https://github.com/Numkil))
* [MediaPagePartBundle] Fix empty urls [#3240](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3240) ([@dannyvw](https://github.com/dannyvw))

## 6.2.3 / 2022-12-15

* [NodeBundle] Revert mysql groupby query change because of performance issues [#3230](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3230) ([@acrobat](https://github.com/acrobat))

## 6.2.2 / 2022-11-28

* [BehatBundle] Fix php version requirement [#3223](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3223) ([@acrobat](https://github.com/acrobat))
* [SeoBundle] Fix saving robots [#3220](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3220) ([@dannyvw](https://github.com/dannyvw))
* [AdminBundle] Store original url so images have the correct url when previewing in editor [#3225](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3225) ([@tarjei](https://github.com/tarjei))
* [BehatBundle] Fix php version requirement [#3222](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3222) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Fix pdf handler priority override of parent service value [#3201](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3201) ([@acrobat](https://github.com/acrobat))

## 6.2.1 / 2022-10-19

* [AllBundles] Revert: "Add php 8.2 to build matrix" [#3195](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3195) ([@acrobat](https://github.com/acrobat))
* [FormBundle] Revert fieldname change and add info to upgrade file [#3194](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3194) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Add php 8.2 to build matrix [#3193](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3193) ([@acrobat](https://github.com/acrobat))
* [NodeBundle] Fix retreiving the page type for both post and get requests with query string [#3192](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3192) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Codestyle fixes [#3185](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3185) ([@acrobat](https://github.com/acrobat))
* [FormBundle] Fix incorrect field name in column attribute [#3184](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3184) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Enable dependabot to update the used github action versions [#3186](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3186) ([@acrobat](https://github.com/acrobat))
* [Dashboardbundle] Explicit int cast to avoid php 8.1 warning [#3181](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3181) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix current request check for forwarded error page requests [#3178](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3178) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Use stable version of subtree splitter and don't limit on path to allow splitting of newly pushed branches [#3174](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3174) ([@acrobat](https://github.com/acrobat))

## 6.2.0 / 2022-10-02

* [AllBundles] Setup php-cs-fixer and add CI check [#3170](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3170) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Replace old doctrine entity notations with fqcn [#3169](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3169) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Replace deprecated entity notation in article twig templates [#3167](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3167) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Remove unnecessary symfony <4 checks [#3166](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3166) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Improve codestyle and types/typehints in generated classes [#3165](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3165) ([@acrobat](https://github.com/acrobat))
* [NodeBundle] PHPStan type improvements [#3164](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3164) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Fix styling issue in generated demo site [#3163](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3163) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Fix return type deprecation warnings in generated sites [#3162](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3162) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Bump minimum supported php version to 8.0 and symfony 5 to 5.4 [#3159](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3159) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Improve phpunit test code [#3158](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3158) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Improve phpdoc and return types for phpstan and symfony deprecations [#3157](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3157) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Upgrade webpack-encore to v4 in layout skeleton [#3156](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3156) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix issues on doctrine/dbal 3 where gedmo TreeRepository class triggers db connection [#3154](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3154) ([@acrobat](https://github.com/acrobat))
* [AllBundles] PHPDoc & typehint improvements for phpstan analysis in projects [#3152](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3152) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Small fixes and improvements to generated files [#3149](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3149) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Add support for php8 attributes in generators [#3148](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3148) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Upgrade webpack encore package [#3140](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3140) ([@dbeerten](https://github.com/dbeerten))
* [GeneratorBundle] Remove unnecessary `getBlockPrefix` method and improve return types [#3130](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3130) ([@acrobat](https://github.com/acrobat))
* [CacheBundle] Fix cachebundle to actually work again with dependency updates [#3122](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3122) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Revert "Forward compatibility fix for v6 symfony/password-hasher" [#3121](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3121) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Forward compatibility fix for v6 symfony/password-hasher [#3119](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3119) ([@acrobat](https://github.com/acrobat))
* [NodeSearchBundle] Out of the box query limit [#3116](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3116) ([@Numkil](https://github.com/Numkil))
* [AllBundles] Remove deprecated entitymanger flush parameter [#3115](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3115) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Replace @Template annotation with controller render [#3111](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3111) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Add groundcontrol / webpack upgrade info [#3110](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3110) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Add twig blocks to authentication layout [#3105](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3105) ([@dannyvw](https://github.com/dannyvw))
* [AdminBundle] Fix php8 deprecation for passing null to stripos [#3103](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3103) ([@acrobat](https://github.com/acrobat))
* [UserManagementBundle] Fix event class issue after upmerge [#3102](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3102) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix compatibility with mysql ONLY_FULL_GROUP_BY sql mode [#3101](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3101) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Don't trigger deprecation on autoload to ease deprecation logging [#3100](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3100) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Upgraded constraints to enable named arguments and attributes [#3094](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3094) ([@acrobat](https://github.com/acrobat))
* [RedirectBundle][SeoBundle][SitemapBundle][TaggingBundle][TranslatorBundle] Add php8 attribute support in entities [#3093](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3093) ([@acrobat](https://github.com/acrobat))
* [VotingBundle] Add php8 attribute support in entities [#3092](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3092) ([@acrobat](https://github.com/acrobat))
* [PagePartBundle] Add php8 attribute support in entities [#3091](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3091) ([@acrobat](https://github.com/acrobat))
* [NodeSearchBundle] Add php8 attribute support in entities [#3090](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3090) ([@acrobat](https://github.com/acrobat))
* [NodeBundle] Add php8 attribute support in entities [#3089](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3089) ([@acrobat](https://github.com/acrobat))
* [MenuBundle] Add php8 attribute support in entities [#3088](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3088) ([@acrobat](https://github.com/acrobat))
* [MediaPagePartBundle] Add php8 attribute support in entities [#3087](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3087) ([@acrobat](https://github.com/acrobat))
* [MediaBundle] Add php8 attribute support in entities [#3086](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3086) ([@acrobat](https://github.com/acrobat))
* [LeadGenerationBundle] Add php8 attribute support in entities [#3085](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3085) ([@acrobat](https://github.com/acrobat))
* [CookieBundle] Add php8 attribute support in entities [#3084](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3084) ([@acrobat](https://github.com/acrobat))
* [FormBundle] Add php8 attribute support in entities [#3083](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3083) ([@acrobat](https://github.com/acrobat))
* [DashboardBundle] Add php8 attribute support in entities [#3082](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3082) ([@acrobat](https://github.com/acrobat))
* [ArticleBundle] Add php8 attribute support in entities [#3081](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3081) ([@acrobat](https://github.com/acrobat))
* [AdminListBundle] Add php8 attribute support in entities [#3080](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3080) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Add php8 attribute support in entities [#3079](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3079) ([@acrobat](https://github.com/acrobat))
* [AdminListBundle] Fix symfony 4.4 incompatible request->all [#3075](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3075) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Handle possible missing encore_entry_exists twig function in legacy groundcontrol FE setup [#3074](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3074) ([@acrobat](https://github.com/acrobat))
* [AdminListbundle] Fix request query get array values deprecation [#3065](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3065) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Revert return constant change [#3064](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3064) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Add `editor-mode` option to wysiwyg form type [#3063](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3063) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix doctrine deprecations and add doctrine/dbal v3 support [#3062](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3062) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Deprecate custom `ColorType` & `RangeType` form types [#3061](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3061) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Replace remaining internal Request::get usages [#3059](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3059) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] Remove leftover concat function on count field [#3058](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3058) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Improve Command classes [#3055](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3055) ([@acrobat](https://github.com/acrobat))
* [NodeBundle] Add `get_node_translation_by_internal_name` twig helper [#3054](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3054) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Deprecate unused admin_password config option [#3053](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3053) ([@acrobat](https://github.com/acrobat))
* [AdminListBundle] Deprecate pager dbal adapter [#3052](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3052) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Remove unused incenteev/composer-parameter-handler dependency [#3051](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3051) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Fix symfony 5.4 security deprecation [#3050](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3050) ([@acrobat](https://github.com/acrobat))
* [AdminBundle] Add support for csrf token on logout url in admin interface [#3040](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3040) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Replace deprecated phpunit mock class method [#3035](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3035) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Fix sf5.4 Request::get internal method usages [#3033](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3033) ([@acrobat](https://github.com/acrobat))
* [GeneratorBundle] Add webpack encore - keep Groundcontrol as option [#2981](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/2981) ([@dbeerten](https://github.com/dbeerten))
* [NodeBundle] Temporary disable csrf check on delete because conflict with other page types [#3153](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3153) ([@acrobat](https://github.com/acrobat))
* [AllBiundles] Update demo site url [#3168](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3168) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Update cms website urls to new domain + update doc urls [#3161](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3161) ([@acrobat](https://github.com/acrobat))
* [RedirectBundle] Save empty domain so redirect router catches all incoming domains for project [#3160](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3160) ([@acrobat](https://github.com/acrobat))
* [AllBundles] Update setup-node action to latest version [#3151](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3151) ([@acrobat](https://github.com/acrobat))
* [TranslatorBundle] Fix sqlite error in translator tests with doctrine/dbal v2.10+ [#3150](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3150) ([@acrobat](https://github.com/acrobat))
