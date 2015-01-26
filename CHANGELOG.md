
3.0.1 / 2015-01-26
==================

  * Merge pull request #130 from jockri/session_security
  * add extra security measures to prevent session hijacking
  * Merge pull request #126 from Maff-/fix-subdir-assets
  * Merge pull request #127 from Maff-/fix-adminlist-add-action
  * Fix redirect add admin action and possible others.
  * Use relative asset paths in stylesheets and asset() twig function
  * Merge pull request #111 from tentwofour/feature_chooserwidgetuseliipimagine
  * Added imagine_filter call in Chooser folder view - was previously loading full image
  * Added imagine_filter call in chooser widget thumnails - was previously loading full image (slow!)
  * Merge pull request #108 from bobhuf/feature/prevent-double-clicks
  * added DisableButtonsOnSubmit function
  * Merge pull request #107 from bobhuf/feature/retina-toggle
  * retina filter toggle
  * Merge pull request #105 from wimvds/fix/live-reload-bundle
  * fix live reload bundle script injector code cleanup fix composer
  * Merge pull request #103 from tentwofour/bugfix_undefinedmethod_setIsEditableNode
  * Update NodeAdminController.php
  * Update .travis.yml
  * Enable 2.6
  * Fix Voting
  * Even more Insight issues
  * Boolean property should not be prefixed by "is"
  * Commented code should not be commited
  * Various Insight updates
  * Fix logic error
  * Unused use statement should be avoided
  * Set the defaults correctly
  * Don't test on master yet
  * Merge pull request #102 from markmedia/addtagfix
  * Merge pull request #101 from markmedia/slugifier
  * add tag fix
  * missing char and string to lower case
  * More fixes for tests
  * Remove to do comments
  * Exclude some envs from the travis builds
  * Remove completely unimplemented generated tests
  * Fix test
  * exit() and die() functions should be avoided
  * Missing use
  * Merge pull request #100 from kimausloos/master
  * Rename nodes_search table
  * Boolean should be compared strictly
  * Error silenced by the at sign (@)
  * Fix imports
  * Text files should end with a newline character
  * Project files should not mix end of lines (fixes #99)
  * Merge pull request #97 from tentwofour/feature_generatecommandnamespacedepth
  * Fix for issue when namespace depth > 1
  * Merge pull request #96 from tentwofour/tentwofourfeature_adminlistconfigurator_superfriendlyname
  * Update AbstractAdminListConfigurator.php
  * Add missing parameter, fixes #95
  * Merge pull request #93 from jockri/add_bulk_actions
  * Merge pull request #92 from jockri/fix_dbal_adminlist_export
  * removed unused class
  * make it possible to add bulk actions on adminlists
  * make it possible to override the ExportService
  * fix dbal adminlist exports
  * Update Installation.md
  * Update README.md
  * Update README.md

3.0.0 / 2014-12-27
==================

  * For an overview of what when in between 2.3 and 3.0, check out [this list on Github](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pulls?q=is%3Apr+is%3Aclosed+milestone%3A%22Q4+2014+-+v3.0%22)

  * Add upgrade files
  * Merge pull request #90 from markmedia/tagging
  * added missing dependency
  * Update .travis.yml
  * Upgrade PHPUnit
  * ensure generator bundle is always installed
  * different package version for generator-bundle
  * Merge pull request #86 from markmedia/slygifier
  * missing coma
  * added missing letters
  * Merge pull request #83 from kimausloos/master
  * Remove unused repo call
  * Merge pull request #81 from markmedia/slugifier
  * Merge pull request #82 from roderik/master
  * Add Symfony 2.6, remove 2.4
  * private to public method change
  * Merge pull request #79 from markmedia/tagfieldoptions
  * Merge pull request #75 from krispypen/fix/translationtests
  * Merge pull request #78 from markmedia/taggingregistry
  * Merge pull request #76 from markmedia/slugifier
  * Merge pull request #77 from markmedia/countrycodes
  * tags field default options
  * admin route names changed to lowercase
  * added estonia and swedish country codes
  * convert russial letters to translit
  * fix failing translation fixtures
  * Merge pull request #66 from Kunstmaan/fix/add-translation
  * Merge pull request #73 from krispypen/fix/nodeforms
  * fix for forms which are nodes and not implement haspageparts interface, later we have to change this to have an adaptformevent in the adminbundle, but that is a breaking change
  * The EntityManager should not be flushed within a loop
  * Missing use statement should be avoided
  * Logical operators should be avoided
  * Logical operators should be avoided
  * Fix generated adminlist controllers
  * Fix case sensitivity error.
  * Temporarily work around the sensio generator bundle changes #71
  * Merge pull request #69 from krispypen/feature/backend_menu_items_should_not_always_have_a_link
  * Merge pull request #70 from krispypen/feature/pageparts_for_non-nodes
  * pageparts should not depend on HasNodeInterface, but on HasPagePartsInterface. Now you can use pageparts (frontend and backend) also for non-node entities
  * menu items in the backend should not always have a link, so now you can group menu items wihout having a link for the group menu item
  * Merge pull request #67 from roderik/master
  * Upgrade CKEditor to 4.4.6 to fix Safari under Yosemite issues
  * fix add translation
  * Merge pull request #65 from kimausloos/master
  * Make slugrouter configurable
  * Merge pull request #64 from Kunstmaan/fix/translation-import
  * fix translation import
  * Merge pull request #63 from wimvds/fix/support-multiple-databases-in-adminlists
  * update upgrade doc ...
  * cleanup skeletons
  * multiple db refactoring ...
  * Merge pull request #62 from wimvds/feature/add-node-repository-helper-functions
  * Merge pull request #61 from wimvds/feature/refactor-admin-menu
  * Merge pull request #60 from wimvds/feature/add-entity-interface
  * Merge pull request #57 from Kunstmaan/fix_search_elastica_host_configuration
  * fix elastica host configuration
  * Update UPGRADE-3.0.md
  * Merge pull request #51 from virtualize/translations_de
  * added german translations
  * Merge pull request #49 from Devolicious/hotfix/user_form_correct_param
  * fix wrong parameter fetch from container
  * Merge pull request #48 from Devolicious/feature/make_upgrade_easier
  * typo
  * upgrade documentation + updates to make upgrading easier
  * fix request scope for search indexation through cli
  * Merge pull request #47 from kimausloos/master
  * Merge pull request #45 from virtualize/article_bundle_fixes
  * Fix travis path
  * entity interface added
  * fixed article table naming in generator bundle, updated deprecated function call
  * Merge pull request #43 from wimvds/hotfix/admin-bundle
  * fix kunstmaan_admin parameters
  * Merge pull request #42 from Kunstmaan/feature/fixes-for-new-bundles-migration
  * check if pdf files still exists before transforming + do not fail on remote video xml
  * fix for wrong highlighting in admin tree / page title / breadcrumbs...
  * add useful node repository helper functions
  * Merge pull request #40 from wimvds/hotfix/search-paging
  * perform full search to fetch number of results
  * Merge pull request #39 from wimvds/feature/refactor-search
  * some refactoring / missing phpDocs added
  * refactor search/nodesearch to use elastica
  * Merge pull request #33 from T4m/reorder-jstree
  * Merge pull request #30 from sebastien-roch/drag-and-drop
  * Merge pull request #36 from wimvds/feature/modify-admin-paths
  * modify admin paths & admin route check
  * Allow to reorder the nodes in Backoffice's sidebar's jstree
  * Merge pull request #31 from sebastien-roch/browser-support-note
  * Added note about browser support on the README file
  * Implemented moving a media through drag-and-drop
  * Highlight drop zone when dragging a media over
  * Merge pull request #26 from sebastien-roch/master
  * Merge pull request #27 from virtualize/demosite_generator_fix
  * Merge pull request #28 from sebastien-roch/fix-analytics-path
  * Merge pull request #29 from virtualize/generator_deprecated_fix
  * fixed deprecated for generate commands
  * fixed path to analytics file
  * fixed path to analytics file
  * fixed demosite db prefixes
  * load uglifyjs2 and uglifycss as vendors through NPM to avoid local installation
  * Merge pull request #18 from wimvds/feature/mimetype-guesser-factory
  * Merge pull request #22 from wimvds/feature/fix-default-site-generator
  * use Finder instead of glob
  * Merge pull request #20 from wimvds/feature/fix-default-site-generator
  * fix copying assets
  * implement mime type guesser factory (so it can be modified on a per project/server basis)
  * Merge pull request #16 from wimvds/feature/add-timestamps-on-nodetranslations
  * Merge pull request #14 from wimvds/hotfix/translator-bundle-preview-translations
  * Merge pull request #13 from wimvds/hotfix/admin-list-ordering
  * Merge pull request #15 from wimvds/feature/refactor-generator-bundle
  * clean up generator bundle / improve page & pagepart generator
  * quickfix: prevent enforcing admin locale in previews fix for anonymous user token
  * bugfixes for admin list orderBy/orderDirection
  * add created and updated timestamps to node translations
  * Merge pull request #11 from lucian-v/master
  * Fix issue with getting order direction from query string
  * Update composer.json
  * Test against Symfony 2.3 2.4 and 2.5
  * Add nelmio/alice
  * Update phpunit.xml.dist
  * Create TestListener.php
  * Also support 2.3 LTS
  * Add the TranslatorBundle's test listener
  * Update composer.json
  * Usage of a function in loops should be avoided
  * Merge pull request #7 from roderik/master
  * Move to minimum-stability: "dev" like symfony/symfony
  * Merge pull request #6 from roderik/master
  * The EntityManager should not be flushed within a loop
  * Files should not be executable
  * Source code should not contain FIXME comments
  * Missing use statement should be avoided
  * PHP configuration should not be changed dynamically
  * Logical operators should be avoided
  * Fix the badge URL
  * Add some badges
  * Merge pull request #2 from roderik/master
  * Moving all bundles to 3.0-dev and depend on ~3.0.0
  * Readd 5.3
  * Remove 5.3 from testing
  * Merge remote-tracking branch 'extrafiles/master'
  * Merge remote-tracking branch 'kunstmaanbundlesdocs/master'
  * Merge remote-tracking branch 'taggingbundle/master'
  * Merge remote-tracking branch 'languagechooserbundle/master'
  * Merge remote-tracking branch 'votingbundle/master'
  * Merge remote-tracking branch 'livereloadbundle/master'
  * Merge remote-tracking branch 'generatorbundle/master'
  * Merge remote-tracking branch 'behatbundle/master'
  * Merge remote-tracking branch 'dashboardbundle/master'
  * Merge remote-tracking branch 'usermanagementbundle/master'
  * Merge remote-tracking branch 'redirectbundle/master'
  * Merge remote-tracking branch 'translatorbundle/master'
  * Merge remote-tracking branch 'utilitiesbundle/master'
  * Merge remote-tracking branch 'sitemapbundle/master'
  * Merge remote-tracking branch 'seobundle/master'
  * Merge remote-tracking branch 'searchbundle/master'
  * Merge remote-tracking branch 'pagepartbundle/master'
  * Merge remote-tracking branch 'nodesearchbundle/master'
  * Merge remote-tracking branch 'nodebundle/master'
  * Merge remote-tracking branch 'mediapagepartbundle/master'
  * Merge remote-tracking branch 'mediabundle/master'
  * Merge remote-tracking branch 'formbundle/master'
  * Merge remote-tracking branch 'articlebundle/master'
  * Merge remote-tracking branch 'adminlistbundle/master'
  * Merge pull request #168 from bureaublauwgeel/master
  * Merge pull request #322 from burki94/patch-1
  * Made the modal views and forms generic just like the chooser.
  * Merge pull request #1 from Kunstmaan/master
  * Merge pull request #167 from kimausloos/master
  * Typo
  * Make remote video services configurable
  * Merge pull request #166 from kimausloos/master
  * Use // and not http/https
  * Remove redundancy
  * fix getting started link
  * fix getting started link
  * fix getting started link
  * fix getting started link
  * fix getting started link
  * fix getting started link
  * fix getting started link
  * fix getting started link
  * fix getting started link
  * Merge pull request #72 from hpatoio/master
  * Fixed wrong link.
  * Update Installation.md
  * Update Installation.md
  * Merge pull request #124 from Kunstmaan/fix_add_new_pagepart
  * fix: when adding a new pagepart with invalid fields, the pageparts was moved outside the page region
  * Merge pull request #162 from Kunstmaan/fix_svg_thumbnails
  * Merge branch 'master' into fix_svg_thumbnails
  * do not try to make thumbnails for svg images on media detail pages
  * do not crop when showing media image on detail page
  * Merge pull request #161 from Kunstmaan/fix_svg_thumbnails
  * Merge pull request #160 from Kunstmaan/fix_empty_name
  * do not try to make thumbnails for svg images
  * use original filename when no name is provided when uploading a file
  * Merge pull request #123 from Kunstmaan/fix-pagepartcreator-service
  * pagepartcreator service should be able to add pageparts to unpublished pages...
  * Merge pull request #159 from Kunstmaan/fix_aviary_save
  * create a new file and kuma_media entry when you click save in the aviary plugin
  * Merge pull request #158 from Kunstmaan/fix-for-custom-metadata
  * fix so you can add custom meta data when importing media...
  * Move to PSR-4
  * Remove implied dependecy
  * Increase stability of kpn-menu and faker
  * Update composer.json
  * fzaninotto/faker has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * Update composer.json
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * knplabs/knp-menu-bundle has a stable version
  * Update composer.json
  * Move to a stable version for fpn/doctrine-extensions-taggable
  * Rename the packagist name
  * Add code coverage for Scrutinizer
  * Just test 5.3
  * Update the travis configuration
  * Setup the phpunit building
  * Extra files for the merged repository
  * Merge pull request #157 from wimvds/master
  * version in doc
  * Folder is nested tree / PDF previews / Keep media name / Move media / Move folder
  * Merge remote-tracking branch 'back/master'
  * Update README.md
  * Merged the VotingBundle into this repository

2.3.x / 2014-08-14
==================

  * Merge pull request #10 from jrobeson/patch-1
  * Merge pull request #11 from jrobeson/patch-2
  * reformat script tag replacement with single quotes
  * Merge pull request #113 from Kunstmaan/hotfix/2.3.17
  * set quotes around included template
  * check all objects
  * Merge pull request #119 from Kunstmaan/pagepart_admin_refactoring
  * scrutinizer ci fixes
  * Merge branch 'master' into pagepart_admin_refactoring
  * allow to render specific parts of a subentity
  * Merge pull request #155 from Kunstmaan/feature/extra-media-fields
  * copyright & description fields added to media
  * extra fields for media (description & copyright)
  * better styling for media form type help text
  * Merge pull request #147 from Kunstmaan/fix-node-twig-extension
  * fix node twig extension
  * Merge pull request #320 from Kunstmaan/feature/nested-sortable
  * Merge pull request #120 from kimausloos/master
  * Merge pull request #146 from Kunstmaan/feature/support-hidden-tree-nodes
  * template fix for "hidden" pages
  * Merge pull request #145 from Kunstmaan/feature/support-hidden-tree-nodes
  * remove comment
  * support hidden tree nodes
  * Merge pull request #122 from jverdeyen/feature/ckeditor-destroy-fix
  * Only destroy CKEDITOR with class = rich_editor
  * Merge pull request #111 from kimausloos/master
  * Merge pull request #28 from Kunstmaan/fix_dashboard_config
  * fix dashboard configuration page
  * update
  * Merge pull request #5 from rvanlaarhoven/patch-1
  * Closed fenced code block
  * Merge pull request #12 from Kunstmaan/sensiolabs-insights
  * Merge pull request #71 from Kunstmaan/feature/fix_export_newline
  * Merge pull request #112 from Kunstmaan/feature/export_trim_whitespaces
  * Merge pull request #319 from Kunstmaan/chosen_width
  * allow overwrite of chosen field width
  * fix multiple choice on export + fix insert database
  * remove spaces
  * Remember adminlist data in session
  * trim new line
  * Don't show choose template when there is only one
  * Merge pull request #318 from jverdeyen/feature/layout_inheritence
  * Add custom js,css header and js footer in layout file
  * Merge pull request #143 from kimausloos/master
  * use psr-4 autoloader
  * Merge pull request #317 from Kunstmaan/update_chosen
  * updated chosen version + allow emty selecten when required
  * sensiolabs insights
  * Merge pull request #43 from Kunstmaan/translator-bugfix
  * Merge pull request #152 from Kunstmaan/fix_iconfont_chooser_class
  * fix iconfont chooser popup window: add extra class
  * Merge pull request #316 from Kunstmaan/fix_form_errors
  * fix form errors for symfony 2.5
  * Fixes StructureNode issues
  * Bugfix for Translator
  * Merge pull request #142 from Kunstmaan/feature/additions
  * added helper function to fetch all node translations for a specific page type
  * small fix (doc & form template)
  * documentation update
  * sorting support for collections...
  * Merge pull request #151 from Kunstmaan/icon_font_field_type
  * code cleanup
  * Merge pull request #150 from Kunstmaan/feature/add-svg-support
  * fix isSupported check
  * implement simple SVG mime type guesser
  * Add grunt-cli
  * Merge pull request #27 from Kunstmaan/feature/default-segments
  * Up dependencies
  * Merge pull request #169 from netounet/patch-1
  * Update and lock all node dependencies
  * Merge pull request #70 from Kunstmaan/fix-broken-dependency
  * removed unused gregwar/form-bundle dependency
  * Fix case sensitivity
  * Fix support for 2.5
  * Merge pull request #313 from Kunstmaan/support-for-html5-range
  * quick CSS fix
  * remove debug dump :p
  * support for HTML5 range input type
  * Update KunstmaanGenerateCommand.php
  * Update GenerateEntityCommand.php
  * Merge pull request #140 from Kunstmaan/feature/additions
  * fix tests
  * Merge pull request #312 from Kunstmaan/feature/multicheckbox-label-fix
  * fix for nested input label, which was broken on linux
  * nodemenu getActive refactoring
  * Merge pull request #9 from Kunstmaan/fix-slugifier
  * No linux support for edge cases... removed for time being
  * fix slugifier ...
  * Merge pull request #110 from sebastien-roch/master
  * Merge pull request #139 from Kunstmaan/fix-twig-extension-naming
  * removed unneeded temp var
  * fix naming / reflect standard url & path functionality in get_..._by_internal_name functions
  * Merge pull request #138 from Kunstmaan/feature/twig_extension_internal_name_support
  * just fetch node translation...
  * extra twig functions to easily fetch internal nodes
  * Merge pull request #41 from Kunstmaan/fix_translation_import
  * fixed javascript error
  * added an icon font form field type
  * Merge pull request #310 from JoakimLofgren/fix-path-lock-file
  * by default, import the translations from all your own bundles when clicking the button in the admin interface
  * Refactor package parsing
  * Do not crash if version checker throws exception
  * Fix path to composer.lock file
  * Merge pull request #26 from Kunstmaan/feature/multiconfig-setup
  * fixed template issue
  * multiconfig fixes
  * Merge pull request #309 from Kunstmaan/feature/allow-service-overrides
  * allow class overrides in admin bundle
  * Merge pull request #168 from jverdeyen/hotfix/dirname
  * Fixes dirname on Linux systems
  * some more refactoring
  * Merge pull request #25 from Kunstmaan/feature/config-setup
  * some more refactoring
  * seperated config template from admin template
  * PagePartAdmin refactoring
  * fixed multiconfig bug in command
  * removed debug stuff
  * fixed multiconfig bug
  * Fixed PHP5.3.3 compatibility
  * multiconfig support
  * added ajax action to remove config
  * added ajax action to remove config
  * added config flush command
  * Merge pull request #24 from Kunstmaan/bugfix/multiconfig-support
  * better code
  * Merge pull request #137 from Kunstmaan/add_node_children_filter
  * Merge pull request #23 from Kunstmaan/bugfix/multiconfig-support
  * fix
  * added multiconfig support
  * filter on hiddenFromNav when requested
  * Merge pull request #167 from netounet/master
  * Merge pull request #22 from sebastien-roch/master
  * Fix PHP 5.3 compatibility
  * Merge pull request #21 from Kunstmaan/feature/optional-goals
  * Merge pull request #306 from Kunstmaan/fix-admin-locale-listener
  * code formatting...
  * fix admin locale listener
  * Fix php 5.3.x compatibility
  * added goal disabling
  * Merge pull request #166 from Kunstmaan/default_site_command_output
  * remove console database update output because it was not in line with getting started documentations
  * made a seperate list
  * added default segments
  * Fix the ordering of stripping and transliteration
  * Merge pull request #30 from Kunstmaan/fix_search_indexing
  * Merge pull request #165 from Kunstmaan/fix_grunt_config
  * in packages.json: devDependencies -> dependencies
  * fix error when indexing ImagePagepart
  * Merge pull request #164 from Kunstmaan/fix_lang_nav
  * fix language navigation in header
  * Merge pull request #163 from T4m/master
  * Bug fix for PHP 5.3
  * Bug fix for PHP 5.3
  * Merge pull request #17 from Kunstmaan/feature/commands
  * Merge pull request #20 from sitron/master
  * fix php 5.3.3 compatibility
  * fix hardcoded path
  * Merge pull request #135 from Kunstmaan/api_changes
  * revert AbstractPage changes
  * Merge pull request #134 from Kunstmaan/api_changes
  * Merge pull request #19 from Kunstmaan/bugfix/segment-issues
  * fixed segment scaling issue
  * moved the id to the AbstractPage class itself
  * Merge pull request #18 from Kunstmaan/bugfix/segment-issues
  * updated commandnames
  * fixed goals not persisting on first savr
  * added invalid segment error message
  * added exception handling when config is not correctly setup
  * fixed a bug when only changing the profileId wouldn't save the config
  * added config name when saving config
  * added selected option of current segment
  * fixed tab styling issue on smaller screens
  * fixed multiconfig bug
  * removed double files
  * name changes
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into feature/commands
  * Merge pull request #13 from Kunstmaan/maintenance/refactoring
  * added more maintenance commands
  * fixed disabling of chosen selects while loading
  * better buttons
  * fixed segment selection w/ chosen
  * added last udpate info for non-super admins
  * added translations
  * fixed chosen setup items
  * fixed chosen bars way too wide
  * added dropdown for segments
  * pretified settings buttons
  * pretified settings buttons
  * re-added chosen
  * added segment edit option
  * Merge pull request #305 from Kunstmaan/fix_acl
  * fix acl query
  * fixed chart data amx value bug
  * fixed setup data bug
  * fixed styling issue
  * fixed styling issue
  * added GA accounts, properties and profiles sorting
  * Merge pull request #147 from Kunstmaan/feature/fix-mimetype-guesser
  * fixed config selection bug
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into maintenance/refactoring
  * Merge pull request #14 from Kunstmaan/bugfix/goal-chartdata
  * merge
  * update
  * Added exception handling
  * fixed inaccurate data
  * fixed inaccurate data
  * fixed bug
  * Code refactoring
  * refactored AJAX route names
  * refactored Entity classes
  * refactored AnalyticsConfigRepository
  * refactored AnalyticsOverviewRepository
  * renamed getGoal() method
  * moved setup.html.twig
  * fix command name
  * Merge pull request #12 from Kunstmaan/maintenance/mergebranch
  * Merge pull request #118 from Kunstmaan/fix-HasPageTemplateInterface
  * fixed invalid entities
  * Fix tests
  * updated docs
  * updated docs
  * updated docs
  * updated docs
  * fixed flash message class
  * added config save flashmessage
  * changed update button behaviour
  * extra check if property has profiles
  * fixed translations
  * removed a space
  * fixed update command
  * fixed connect page
  * fixed class name bug
  * moved repository methods
  * Use FileBinaryMimeTypeGuesser first
  * updated documentation
  * updated documentation
  * updated documentation
  * updated documentation
  * updated documentation
  * updated documentation
  * fixed update bug
  * renamed commands
  * updated documentation
  * updated documentation
  * updated documentation
  * updated documentation
  * Updated documentation
  * cleaned some code
  * bugfix command
  * cleaned command code
  * Add container to repo to avoid method on non object error
  * cleaned code
  * Added overview update link
  * Better config setup flow
  * added translation
  * updated UPGRADE instructions
  * updated UPGRADE instructions
  * config setup changes
  * config setup changes
  * Init page
  * Added upgrade message
  * Merge pull request #133 from Kunstmaan/nodemenu_performance
  * Added BC support
  * Added BC support
  * code cleanup
  * added config flush command
  * added segment update support from the dashboard
  * fixed new overviews not added to config and segment object
  * Removed data fixtures
  * fixed styling issue with tabs
  * fixed bug with configHelper
  * fixes
  * merge with branches
  * fixed users metric inconcistency when not a full year dataset is available
  * Merge branch 'maintenance/ga-dashboard-helpers' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into bugfix-data-inconsistency
  * Added active profile getter
  * merge with new helper branch
  * merge with new helper branch
  * Fixed command options
  * Merge pull request #117 from Kunstmaan/fetch_pageparts_performance
  * removed command params
  * Merge pull request #8 from JoakimLofgren/use-request-pathinfo
  * Merge pull request #301 from WouterJ/xml_support
  * merge
  * merge with master
  * merge with master
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into bugfix-data-inconsistency
  * removed test function
  * fixed year-to-date overview
  * Merge pull request #10 from Kunstmaan/bugfix/resetgoals
  * goal reset bugfix
  * update
  * Added option params to update command
  * removed some unneeded AJAX calls
  * Added segment option in command
  * Added segment selection support
  * added backend support for segments
  * Merge pull request #40 from Kunstmaan/debug_mode
  * make it possible to enable/disable the debug mode via config.yml
  * added frontend support for segments
  * only do one query for each pagepart type instead of a query per pagepart
  * Fix screenshot in documentation
  * update
  * update
  * Merge pull request #116 from JoakimLofgren/fix-pagetemplate-clone
  * Add test cases for CloneListener
  * added some css
  * refactor getNodeByInternalName
  * reversed change, still bugged
  * Import HasPageTemplateInterface
  * fixed remarks
  * loopup parent menu item when not calculated yet
  * do not do extra queries for fetching children
  * AJAX config setup
  * store all nodes in temporary variable + refactoring
  * AJAX setup
  * added ajax controller
  * Added better helper classes and services
  * Merge branch 'ga-dashboard-collection' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard-collection
  * support for media query-specific class
  * Better config name support
  * Merge pull request #109 from Kunstmaan/extra_parameters_when_filtering
  * Merge pull request #145 from Kunstmaan/fix_adminlist_routing
  * Merge pull request #146 from Kunstmaan/fix_redirect
  * fix the redirect to the last used folder when the folder was deleted
  * also post the existing get parameters when filtering an admin list
  * the adminlist is used for both the media browse as the media choose controller/routing
  * Merge branch 'ga-dashboard-collection' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard-collection
  * footer fix
  * fixed clearing goals
  * multiconfig update button
  * Merge pull request #69 from Kunstmaan/fix_export_encoding
  * always use UTF-8 for exports
  * fixed no-data available tab bug
  * fixed multi-config support
  * fixed multi-config support
  * Merge pull request #304 from kimausloos/feature/fix-superadmin
  * fixed multi-config support
  * Fix superadmin group
  * merge with master
  * merge with master
  * Update DashboardAnalyticsWidgetSetup.md
  * Add tests for Url locale guesser
  * Use request path info as fallback
  * Logical ordering
  * Move the code to the GA classes
  * Fix and improve goal charts
  * Remove the current day
  * Correct the loading of the goals
  * Fix wrong user metrics
  * Source formatting
  * Huge code cleanup based on PHPStorm inspections
  * Upgrade morris to fix the graphing errors on resize
  * Remove conversions label
  * Tweak first run and loading behaviour
  * fixed multi-config support
  * Merge pull request #7 from Kunstmaan/ga-dashboard-maintenance
  * Merge remote-tracking branch 'origin/ga-dashboard-maintenance' into ga-dashboard-collection
  * Merge branch 'ga-dashboard-maintenance' into ga-dashboard-collection
  * update
  * fixed empty date bug
  * config name support
  * Merge remote-tracking branch 'origin/master' into ga-dashboard-collection
  * update
  * Merge pull request #6 from Kunstmaan/ga-dashboard-maintenance
  * added update link
  * added back button in property and profile selection
  * fixed ymax in max < 100
  * fixed  not found
  * Merge pull request #5 from Kunstmaan/ga-dashboard-bugfix-ratelimit
  * Merge branch 'ga-dashboard-collection' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard-collection
  * fixed ratelimit bug
  * Update messages.en.yml
  * Update DashboardAnalyticsWidgetSetup.md
  * Merge pull request #159 from Kunstmaan/feature/styling-update-default-site
  * update default styling generator bundle of the slider
  * added multi config support
  * Merge pull request #302 from Kunstmaan/feature/dashboardstyling
  * Merge remote-tracking branch 'origin/master' into feature/dashboardstyling
  * Merge pull request #4 from Kunstmaan/ga-dashboard
  * Merge remote-tracking branch 'origin/master' into ga-dashboard
  * Fix chart height
  * goalchart bugfix
  * tabswitch bugfix
  * goal boxes in twig
  * added uglifyjs again
  * update
  * update
  * YUI compressor is depreciated, moving to UglifyJS/CSS
  * Add uglifyjs
  * Fixed new session metric
  * Pretty
  * Fix job
  * Small fixes
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * Add translations and optimise the javascript via uglifyjs
  * Fixed avgPagesPerSession metric
  * update
  * update
  * update
  * update
  * cleaned some code
  * Fix yesterday fixture
  * Add the container for containerawarecommand
  * Fix depreciated method
  * Prevent vendors to be checked in
  * Don't commit vendors!
  * Fix wrong import in the fixtures
  * update
  * update responsive styling
  * update
  * update chzn
  * gi tpMerge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * update desing
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * year-to-date overview
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * rm
  * update
  * removed unused code
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * removed unused files
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * update
  * stat chagnes
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * fixed some chart issues
  * update
  * Merge pull request #300 from Kunstmaan/fix-circular-dependency
  * Added docblocks
  * Merge pull request #1 from Kunstmaan/user_form_fixes
  * minor fixes user add/edit form
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * fixed chart label bug
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * update styling
  * made update command more modular
  * update command fixed
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * cleaned controller code
  * improved Goals query
  * update
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanUserManagementBundle
  * fix typo
  * Merge branch 'ga-dashboard' of git://github.com/Kunstmaan/KunstmaanDashboardBundle into ga-dashboard
  * update
  * fixed area chart bug
  * Added XML support
  * Update composer.json
  * add fos change user password route
  * Merge pull request #131 from Kunstmaan/feature/remove_online_changes_warning
  * remove warning online changes
  * request bug
  * fixed some more routing and splitted controller logic
  * fixed loading bug
  * refactored / extra assets added (Travis/phpunit)
  * fixed command imports
  * Merge pull request #9 from Kunstmaan/fix-take-screenshot-on-fail
  * Merge branch 'master' into fix-circular-dependency
  * message if no data available
  * Merge pull request #299 from Kunstmaan/feature/insights
  * fix filename timestamp
  * taking screenshots does not work as subcontext / moved it back to main context
  * namechanges
  * Added new implementation for goal updates, do not remove!
  * Added docs
  * Added docs
  * updated the command tot kuma:dashboard:widget:googleanalytics
  * fixed charts not clearing
  * routing fix
  * routing fix
  * Merge pull request #130 from Kunstmaan/fix_menu_order
  * merge
  * bugfix
  * menu items are sorted via the weight parameter in nodeTranslation
  * code cleanup & refactoring / admin locale migration & fixtures
  * Fix error
  * Fix some errors
  * Merge pull request #3 from brentroose/ga-dashboard
  * merge
  * Delete composer.lock
  * add change password route
  * Merge pull request #2 from brentroose/ga-dashboard
  * merge
  * Added all new things
  * Unused use statements and variables
  * Commented code should not be commited
  * Source code should not contain TODO comments
  * Limit the flush action
  * Files should not be executable
  * Remove CKEditor samples
  * circular dependency with adminlist removed menu ordering using priority in service
  * menu ordering / set parent menu active to keep sidebar
  * Merge pull request #158 from Kunstmaan/slider-update
  * initial test version - some issues...
  * html data attr update
  * Merge pull request #295 from Kunstmaan/admin_locale_listener
  * use bundle parameters instead of global parameters + refactoring
  * updated slider view
  * Merge branch 'master' into admin_locale_listener
  * added some comments
  * code cleaned
  * Merge pull request #298 from Kunstmaan/fix_dashboard_route_config
  * fix error when dashboard_route is not configured in config.yml
  * cleaned some code
  * Initial commit
  * Merge branch 'master' into admin_locale_listener
  * fixed update command bug
  * final changes
  * Merge pull request #294 from roderik/master
  * Merge pull request #296 from Kunstmaan/acl_performance_improvement
  * Merge pull request #2 from Kunstmaan/fix-unit-tests-for-php53
  * Merge pull request #24 from Kunstmaan/db_indexes
  * Merge pull request #115 from Kunstmaan/db_indexes
  * Merge pull request #38 from Kunstmaan/db_indexes
  * Merge pull request #129 from Kunstmaan/db_indexes
  * Merge pull request #297 from Kunstmaan/db_indexes
  * performance improvements: added db indexes
  * fix unit tests for php 5.3
  * performance improvements: added db indexes
  * performance improvements: added db indexes
  * performance improvements: added db indexes + more strict field types
  * performance improvements: added db indexes
  * Fix dev dependency
  * tabs click
  * Fix bourbon loading
  * added lots of stuff
  * Increase our default support to IE9 and up
  * Remove empty description field since it's generated by the SEO bundle
  * Remove socialite and social sharing buttons from the default setup
  * Also render the .bowerrc file
  * progress
  * Merge pull request #155 from Kunstmaan/feature/add-correct-errorpages-14115
  * Merge pull request #157 from Kunstmaan/feature/move-bower-to-generator
  * Move bower configs to the generator and update+fix the bower dependencies
  * Added new metrics
  * Merge pull request #128 from Kunstmaan/fix_slug_controller
  * fallback for when _nodeTranslation is not set
  * Merge pull request #1 from wimvds/unit-tests
  * Merge pull request #108 from Kunstmaan/unit-tests
  * corrected / refactored / added unit tests
  * needed to mock filterbuilder in unit tests...
  * Merge pull request #2 from Kunstmaan/update-docs
  * faster acl query
  * update/restructure installation documentation
  * Add Travis file
  * Initial version of the Redirect bundle
  * Merge pull request #156 from Kunstmaan/fix-default-site-generator-translations
  * fix translations
  * Fixed chart inaccurecy
  * fixed css
  * Added GA widget
  * Configure errorpages correctly
  * composer.json update
  * Merge branch 'master' of github.com:brentroose/KunstmaanDashboardBundle into ga-dashboard
  * updated composer.json
  * Added files
  * Wrong Classname
  * added dependencies
  * Allow to override the admin dashboard (for use with the DashboardBundle)
  * Create README.md
  * Inital framework is set, prepared to add real dashboard features
  * Merge pull request #37 from Kunstmaan/fix-translator-loader
  * fix translator loader bug (load translations per locale & cache catalogue per locale)
  * Merge pull request #7 from Kunstmaan/feature/newrelic-namingstrategy
  * Add the New Relic naming strategy
  * Merge pull request #107 from Kunstmaan/feature/datetime-filter-type
  * Merge pull request #105 from Kunstmaan/enum_filter_translation
  * Merge branch 'master' into feature/datetime-filter-type
  * unit tests for datetime filter
  * Merge pull request #106 from Kunstmaan/feature/datetime-filter-type
  * Merge pull request #293 from Kunstmaan/support-indexed-arrays-in-filter
  * date time filter
  * support indexed arrays in filters
  * make it possible to translate the enum filter select box values
  * Merge pull request #127 from Kunstmaan/feature/fixNodeRepository
  * fix getAllTopNodes query
  * Up the minimum version of PHP to fix #64
  * Update composer.json
  * Update composer.json
  * Merge pull request #144 from Kunstmaan/fix-mime-type-guesser
  * Update composer.json
  * Merge pull request #126 from alde/add-internal-name-field
  * Add internal name field
  * Merge pull request #113 from Kunstmaan/feature/multiplePagePartTemplates
  * Merge pull request #36 from mozzymoz/patch-1
  * Merge branch 'add-url-to-getAllMenuNodes' of https://github.com/alde/KunstmaanNodeBundle into add-url-to-getAllMenuNodes
  * Update composer.json
  * code reformatted / removed deprecated getRequest
  * Reversed node translation check back to SlugRouter NodeTranslation is now stored in the Router as _nodeTrasnaltion parameter
  * Noderepository now fetching translations and publicnodeversion wherever possible
  * Hydrate public node version
  * Let Controller validate translation
  * Remove weight order
  * Merge pull request #116 from mozzymoz/patch-1
  * update method naming
  * ensure BC
  * implemented new override method
  * use mime type guesser
  * Update Installation.md
  * Stable version
  * Merge pull request #143 from Kunstmaan/fix-foldertype-parent-required
  * fix for php 5.3 compatibility
  * code reformatting/cleanup & fix parent required in foldertype
  * Update Installation.md
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Merge pull request #291 from Kunstmaan/feature/move-to-2.4-and-beyond
  * Allow Symfony 2.4 and higher
  * Upgrade Behat and Mink
  * placeholder.min is not in the vendor anymore
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Fix dev dependency
  * Merge branch 'master' of https://github.com/Kunstmaan/KunstmaanNodeSearchBundle
  * Update travis
  * Merge pull request #14 from roderik/master
  * Update composer.json
  * allow to override the default pp template with variable template
  * Stability
  * Stability
  * Stability
  * Stability
  * dev dependencies & Travis
  * Stability - composer
  * Stability
  * Stability
  * fix composer & unit tests
  * fix unit test
  * Merge pull request #125 from roderik/master
  * Some fixes
  * Update composer.json
  * Update README.md
  * Merge pull request #29 from roderik/master
  * Tweak travis
  * Update composer.json
  * Merge pull request #9 from roderik/master
  * Update travix
  * Update composer.json
  * Update .travis.yml
  * Merge pull request #141 from roderik/master
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Merge pull request #154 from roderik/master
  * Increase minimum stability of this bundle, and try to use only stable tagged versions of dependencies. Targetted at Symfony 2.3 and the 2.3 series of Kunstmaan bundles.
  * Update composer.json
  * Update README.md
  * Update .travis.yml
  * Merge pull request #68 from roderik/master
  * Increase minimum stability of this bundle, and try to use only stable tagged versions of dependencies. Targetted at Symfony 2.3 and the 2.3 series of Kunstmaan bundles.
  * Update .travis.yml
  * Merge pull request #8 from roderik/master
  * Increase minimum stability of this bundle, and try to use only stable tagged versions of dependencies. Targetted at Symfony 2.3 and the 2.3 series of Kunstmaan bundles.
  * Update .travis.yml
  * Update README.md
  * Update README.md
  * Merge pull request #15 from roderik/master
  * Increase minimum stability of this bundle, and try to use only stable tagged versions of dependencies. Targetted at Symfony 2.3 and the 2.3 series of Kunstmaan bundles.
  * fix unit test for stable version
  * Merge pull request #32 from mozzymoz/feature/queryoptimisation
  * Merge pull request #34 from roderik/master
  * Increase minimum stability of this bundle, and try to use only stable tagged versions of dependencies. Targetted at Symfony 2.3 and the 2.3 series of Kunstmaan bundles.
  * Update README.md
  * Merge pull request #289 from roderik/master
  * Increase minimum stability of this bundle, and try to use only stable tagged versions of dependencies. Targetted at Symfony 2.3 and the 2.3 series of Kunstmaan bundles.
  * You can now set an AdminListConfigurator it to not prefix the countColumn with DISTINCT.
  * Update README.md
  * Merge pull request #104 from roderik/master
  * Increase minimum stability of this bundle, and try to use only stable tagged versions of dependencies. Targetted at Symfony 2.3 and the 2.3 series of Kunstmaan bundles.
  * Revert "Stability"
  * Revert "Stability"
  * Stability
  * Stability
  * Stability
  * Stability
  * Stability
  * Stability
  * Revert "Stability"
  * Revert "Stability"
  * Stability
  * Stability
  * Stability
  * Stability
  * Stability
  * Stability
  * Merge pull request #288 from Kunstmaan/issue_errorclass_richtextbox
  * Added outline color to empty rich textbox
  * Merge pull request #124 from Devolicious/feature/offlineLinkChooser
  * show offline translated pages in link chooser
  * Merge pull request #140 from Kunstmaan/image_metadata
  * metadata for images
  * Always include hidden, as to not change the return format based on a flag
  * Include hidden if it is hidden from nav
  * Add url to getAllMenuNodes
  * Merge pull request #121 from JoakimLofgren/fix-create-empty-translation-page
  * Fix creation of NodeEvent object
  * Fix add empty page translation log
  * Retrieve all translations for domain
  * Add domain field to index
  * Merge pull request #287 from JoakimLofgren/fix-acl-helper
  * Add unit tests for anonymous user
  * Fix so anonymous user works in frontend
  * inject params instead of Container
  * Added docblocks
  * Added seperate adminlocale support and kuma:user:create command support
  * Merge pull request #284 from JoakimLofgren/add-role-hierarchy-support
  * Added docblocks
  * Added per-user language support
  * Added per-user language selection
  * Merge pull request #31 from Kunstmaan/fix_translator_filter
  * add text filter
  * fix locale not contains filter
  * Merge pull request #67 from Kunstmaan/feature/excelExport
  * Merge pull request #103 from Kunstmaan/feature/exportlist
  * Merge pull request #285 from Kunstmaan/ie8-browser-support
  * make admin presentable in ie8
  * Add test cases for AclHelpers
  * Use the role hierarchy to get user roles.
  * new listconfigurator for exports
  * use export service from admin list bundle to export multiple formats
  * Merge pull request #283 from Kunstmaan/fix-console-log
  * Remove console.log
  * Merge pull request #120 from Kunstmaan/allow-admintype-services
  * Allow admintype services like in the pagepart bundle
  * Merge pull request #281 from Kunstmaan/issue14965-image-popup-ie9-noscroll
  * get scrollbar in IE9 image chooser popup
  * Revert "Merge branch 'issue14713-dashboard' of git://github.com/Kunstmaan/KunstmaanAdminBundle"
  * Merge branch 'issue14713-dashboard' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * Merge pull request #118 from mozzymoz/patch-2
  * Merge pull request #102 from mozzymoz/patch-1
  * Merge pull request #153 from Kunstmaan/test-page-previews
  * Merge pull request #119 from Kunstmaan/previews-in-admin
  * Merge pull request #107 from Kunstmaan/feature/sensio-insights
  * Merge pull request #139 from Kunstmaan/bulk_upload_max_file_size
  * increase total filsize limitation for bulk uploads
  * move preview path to admin section
  * test page previews
  * Merge pull request #152 from Kunstmaan/fix-admin-tests
  * travis test env displays other errors
  * Merge pull request #151 from Kunstmaan/fix-admin-tests
  * fix tests
  * fix tests
  * check page contents instead of response codes for the time being
  * WebDriver does not have support for reading http response status codes, but goutte does...
  * Merge pull request #280 from Kunstmaan/ie9-scrollbar-in-sidebar-fix
  * get rid of unnecessary scrollbars in sidebar IE9 + align checkbox to the left in IE9
  * Merge pull request #278 from Kunstmaan/update-ckeditor
  * 970px wide popup screen when clicking browse image to fit the 960px wide content
  * Return any response given from Entity
  * Merge pull request #6 from roderik/master
  * Minimal stability stable and allow for Symfony 2.4 and higher
  * Merge pull request #117 from Kunstmaan/link_chooser_performance
  * do only one query to render node tree in link chooser popup
  * Add reset Method for built members
  * new ckeditor
  * NodeAdminController: Make list overridable
  * Merge pull request #28 from JoakimLofgren/fix-fixtures-issue
  * Merge pull request #30 from Kunstmaan/fix_translation_filtering
  * fix filtering functionality in translation editor
  * Merge pull request #277 from Kunstmaan/fix_add_user
  * fix add user: allow group assignment
  * Merge pull request #275 from Kunstmaan/edit_user_permissions
  * a user should only be able to change his own password/username/email
  * Merge pull request #106 from hellomedia/master
  * Merge pull request #27 from hellomedia/master
  * Merge pull request #150 from Kunstmaan/fix-languagechooser-dependency
  * remove language chooser config (no longer in use)
  * Update fixtures to include translation ids (issue #27)
  * Merge branch 'master' into issue14713-dashboard
  * IE8 fixes + responsive cols
  * restructure for non calc and non flexbox supporting browsers + start IE8 fixing
  * Merge pull request #273 from Kunstmaan/media-chooser-popup-fix
  * fix for scroll issue, file chooser popup list
  * dashboard chart
  * Merge pull request #26 from Kunstmaan/permission_check
  * fix the translations edit page
  * styling dashboard
  * check that it is possible to edit the translation inline
  * Merge pull request #272 from Kunstmaan/feature/fix-settings-for-non-superadmin
  * Fix settings for non superadmin roles
  * Merge pull request #271 from Kunstmaan/fix-treeview
  * use sane IDs in tree view (with backwards compatibility)
  * Merge pull request #270 from Kunstmaan/fix-enum-filter
  * fix for enum filter
  * Merge pull request #138 from Kunstmaan/feature/tooltip
  * translate tooltip
  * fix typo readme
  * update readme
  * tooltip added for media_widget
  * Merge pull request #137 from Kunstmaan/media_detail_view
  * do not show list view table header when there are no files in the folder
  * Merge pull request #95 from Kunstmaan/fix/enumerationfilter-template
  * remove debug dump from enumerationfilter twig template...
  * Merge pull request #136 from Kunstmaan/remember_last_folder
  * Remember the last visited folder in the media chooser popup
  * Merge pull request #135 from Kunstmaan/media_detail_view
  * Merge pull request #269 from Kunstmaan/media_detail_view
  * added some blocks in the main layout to be more flexible
  * css fixes for dropdown button groups
  * Merge pull request #24 from hellomedia/master
  * better adminlist action icon position
  * Merge pull request #25 from Kunstmaan/feature/edit-translations-inline
  * update upgrade doc
  * upgrade instructions
  * enforce phpunit 3.x to fix code coverage on php 5.3
  * fix diff command
  * list view for media chooser popup
  * fix tests
  * Merge pull request #94 from Kunstmaan/feature/enumeration-filter
  * support for enumeration filter
  * admin list with inplace edit / delete / add support
  * list view for folder views
  * Merge branch 'master' into media_detail_view
  * Merge pull request #134 from Kunstmaan/multi_file_uploader
  * Merge pull request #268 from Kunstmaan/multi_file_uploader
  * added plupload library
  * fix bulk upload functionality for ie8 and ie9
  * Merge pull request #134 from Kunstmaan/fix-field-naming-check
  * Merge branch 'master' into media_detail_view
  * create admin list configurator
  * Merge pull request #133 from Kunstmaan/fix_drop_upload
  * fixed drop upload functionality
  * Rename selectLinkrectreeview.html.twig to selectLinkRecTreeView.html.twig
  * fixed screenshot in README
  * Merge pull request #149 from Kunstmaan/pp_genertor_behat
  * Merge pull request #132 from Kunstmaan/media_link_chooser
  * Merge pull request #113 from Kunstmaan/media_link_chooser
  * fix links
  * make it possible to select a media file in the linkChooser
  * make it possible to select a media file in the linkChooser
  * make sure the BehatTestPage gets removed even if the pagepart test fails
  * Merge pull request #93 from Devolicious/feature/exportTemplateRenderFix
  * render template during export for proxy objects
  * Merge pull request #147 from Devolicious/feature/exportTypes
  * Merge pull request #28 from Devolicious/feature/audioPagePart
  * Merge pull request #148 from Devolicious/feature/audioPagePart
  * added audio pagepart to the pagepart configs
  * added translations for audio
  * added new audio page part
  * Merge pull request #91 from Devolicious/feature/exportXls
  * use static method for export extensions
  * make supported extensions method static
  * seperate response from document creation
  * fetch supported export extensions from adminlistbundle if present else default to csv
  * Excel export extension available New twig function 'supported_export_extensions' New dependency on phpoffice/phpexcel
  * Merge pull request #90 from jverdeyen/feature/block-mainactions-addedit
  * Wrap main actions in add/edit template in a block
  * Merge pull request #131 from Devolicious/feature/audio
  * update readme
  * delete unnecessary code
  * new remote audio handler for soundcloud
  * Update Gruntfile.js
  * Merge pull request #145 from Kunstmaan/new-structures-everything-in-grunt
  * Merge pull request #89 from jverdeyen/feature/extend-add-edit
  * Extend from add_or_edit.html.twig I should be able to only overwrite the form
  * restructure grunt generated files to .temp folder + adjust all paths
  * add outdated browser message to layout
  * add grunt tasks to package.json
  * new gruntfile
  * add new line at end of file
  * add french translations
  * add french translations
  * add french translations
  * inline editing done
  * Merge pull request #267 from Kunstmaan/cut-off-chosen-dropdown-fix
  * change min-height content to 600px to avoid dropdown being cut off
  * Merge pull request #14 from Kunstmaan/dynamic_user_class
  * Merge pull request #111 from Kunstmaan/dynamic_user_class
  * Merge pull request #266 from Kunstmaan/dynamic_user_class
  * fixed naming
  * made the admin user entity class dynamic
  * made the admin user entity class dynamic
  * made the admin user entity class dynamic
  * Merge pull request #245 from Kunstmaan/feature/sensio-insights
  * Merge pull request #265 from Kunstmaan/fix-admin-nested-choice
  * fix nested input checkbox and radiobuttons
  * Merge pull request #264 from Kunstmaan/improve_reset_password
  * better password reset subject
  * Merge pull request #263 from jverdeyen/feature/colorpicker
  * Fixed colorpicker image path
  * Colorpicker images
  * Add colorpicker form type
  * Merge pull request #262 from Kunstmaan/use_chosen_select
  * use chosen select in settings section for selecting roles and groups
  * Merge pull request #261 from Kunstmaan/improve_reset_password
  * fix for multi language sites
  * Merge pull request #260 from Kunstmaan/improve_reset_password
  * improvements forgot password process
  * Merge pull request #88 from Kunstmaan/fix-add-edit
  * Merge pull request #144 from Kunstmaan/fix-adminlist-template
  * Merge pull request #143 from Kunstmaan/generator-spacing
  * fixed admin list controllers
  * refactored adminlist add/edit template (#14038)
  * fix page generator default settings
  * Merge pull request #142 from Kunstmaan/media-field-handling
  * modify media field handling (#14045 & #14048)
  * Merge pull request #141 from Kunstmaan/remove-template-annotations
  * Merge branch 'master' into remove-template-annotations
  * removed Template annotations (#14029)
  * Merge pull request #140 from Kunstmaan/remove-template-annotations
  * removed Template annotations (#14029)
  * Demosite parameter
  * Merge pull request #105 from Kunstmaan/feature/adj-header-pp
  * Merge pull request #139 from Kunstmaan/check_page_methods
  * pagepart generator: check that page has required functions for behat test generation
  * Merge pull request #110 from Kunstmaan/custom-redirects-after-delete
  * add support for custom redirect after delete
  * Merge pull request #130 from Kunstmaan/fix_file_chooser_bug
  * Merge pull request #22 from Kunstmaan/translate_validators_domain
  * make it possible to translate error messages
  * in the media file chooser, when you have multiple file types in one directory, only one type was shown
  * Merge pull request #22 from Kunstmaan/feature/websitetitle
  * Merge pull request #259 from jverdeyen/feature/info_text
  * Allow a questionmark with tooltip next to an input item
  * Seo website title can contain a websitetitle parameter placeholder
  * Merge pull request #258 from Kunstmaan/fix/pagepart-subentities
  * enable ckeditor on newly created sub entities
  * fix for subentities in pageparts
  * remove old method and add id to header
  * Merge pull request #257 from Kunstmaan/fix_media_popup_scroll
  * Merge pull request #129 from Kunstmaan/feature/images/scroll
  * Fix for the scrolling issue in image chooser pagepart
  * Added a class to the images container when choosing an image for pagepart
  * Merge pull request #256 from Kunstmaan/feature/fix-nested-entity-form
  * extra class needed to properly align nested entities in forms
  * Merge pull request #254 from Kunstmaan/fix_pp_overflow
  * Merge pull request #104 from Kunstmaan/new_pp_init_chosen
  * fix overflow when having tooltips or chosen lists that should go outside of the block
  * initiate the chosen js plugin when you add a new pagepart (via ajax)
  * Merge pull request #253 from Kunstmaan/feature/add_bootstrap_3_grid_fallback
  * Merge pull request #137 from Kunstmaan/add_simple_behat_link_check
  * fixed if structure
  * added behat function to check if a link is shown on a page
  * Added bootstrap overrides for 'cols' to act like 'spans' from V 2.3.2.
  * Merge pull request #247 from Kunstmaan/update_label_form_field
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Merge pull request #249 from Kunstmaan/fix-sub-entities-handling
  * fix sub entities handling - get rid of multiple delete buttons / add new repetitions
  * Merge pull request #109 from Kunstmaan/fix-draft-publishing
  * fix draft publishing
  * removed unnecessary a tag
  * Update Gruntfile.js
  * Update Gruntfile.js
  * Merge pull request #133 from Kunstmaan/feature/googletagmanager
  * Update variable
  * Merge pull request #248 from Kunstmaan/fluent-interface-support
  * Merge pull request #21 from Kunstmaan/fluent-interface-support
  * Merge pull request #108 from Kunstmaan/fluent-interface-support
  * Merge pull request #128 from Kunstmaan/fluent-interface-support
  * Merge pull request #66 from Kunstmaan/fluent-interface-support
  * support for fluent interface
  * support for fluent interface
  * support for fluent interface
  * support for fluent interface
  * support for fluent interface
  * Merge pull request #100 from Kunstmaan/fix-node-listener
  * Merge pull request #103 from Kunstmaan/fluent-interface-support
  * Merge pull request #26 from Kunstmaan/fluent-interface-support
  * pageparts should support fluent interface
  * pageparts should support fluent interface
  * Merge pull request #127 from Kunstmaan/media_form_field_changes
  * make it possible to add the class/style attributes in media form fields
  * make it possible to hide form label
  * Merge pull request #246 from Kunstmaan/fix-locale-switcher-widget
  * fix locale switcher widget
  * SettingsController refactored : created separate controllers for Settings/Users/Groups/Roles
  * changes for Sensio Insights
  * Merge pull request #7 from hellomedia/patch-1
  * just a small typo :)
  * Merge pull request #126 from Kunstmaan/fix_bulk_upload
  * show nice error when no file is selected and the form is submitted
  * Check for 404
  * Fix the test
  * Merge files
  * Add sass compilation
  * Tune the grunt file
  * Go with the latest and greatest
  * I should probably test before committing…
  * Error in the grunt file
  * Again tweak the grunt file
  * Fix up grunt-modernizr
  * Fix table names
  * No longer allow failures on php 5.5
  * Fix for —no-interaction
  * Fix site generation with —no-interaction
  * Merge pull request #136 from Kunstmaan/behat_pageparts
  * Merge pull request #102 from Kunstmaan/deep-cloning-fix
  * Merge pull request #244 from Kunstmaan/deep-cloning-fix
  * fix for deep cloning in pageparts (needed for sub entities)
  * add DeepCloneInterface to solve deep cloning issues
  * Merge branch 'master' into behat_pageparts
  * create behat tests for generated pageparts
  * Merge pull request #7 from inmarelibero/patch-1
  * Merge pull request #243 from Kunstmaan/behat_pp_test
  * Merge pull request #101 from Kunstmaan/behat_pp_test
  * Merge pull request #105 from Kunstmaan/behat_pp_test
  * added selector for pagepart behat tests
  * added selector for pagepart behat tests
  * added selector for pagepart behat tests
  * fixed typo
  * fill in fiel fields with spaced labels
  * Merge pull request #135 from Kunstmaan/fix-faker-dependency
  * fix faker dependency - needed for image provider...
  * field names can contain numbers as well (but should start with a letter)
  * add option to change the page template via behat
  * Fix needed for backwards compatibility
  * Add google tag manager
  * added context functions to test pageparts
  * always add the new page as sub-page of the BehatTestPage
  * added BehatTestPage for running pagepart behats tests
  * Merge pull request #132 from Kunstmaan/admin_list_format_improvements
  * fix DI breaking commit
  * Removed var_dumps,
  * Two more Insight fixes
  * Fix insight issues
  * add missing license file...
  * fix dependencies
  * fix for BC break
  * Update Installation.md
  * lower case routing configuration
  * admin list generation formatting changes
  * Fix for #63: ChoiceFormSubmissionField:__toString() returns empty string when value is not an array
  * Merge pull request #242 from Kunstmaan/fix_datepicker
  * fix datetime input style + media input box error
  * make sure the datepicker also works when adding new pageparts (ajax)
  * Merge pull request #241 from Kunstmaan/add_sass_files
  * added sass files for styling the admin interface
  * Merge pull request #65 from Kunstmaan/63-ChoiceFormSubmissionField-returns-empty-string
  * Fix for #63: ChoiceFormSubmissionField:__toString() returns empty string when value is not an array
  * Merge pull request #123 from Kunstmaan/fix_folder_update
  * Merge pull request #124 from Kunstmaan/remove_unused_blocks
  * Merge pull request #240 from Kunstmaan/remove_unused_blocks
  * Merge pull request #104 from Kunstmaan/refactoring
  * use User object instead of userId in QueuedNodeTranslationAction entity
  * removed unused twig block
  * removed unused nodemenu function calls
  * removed unused twig blocks
  * removed unused twig blocks
  * fix folder update action
  * Merge pull request #103 from hellomedia/master
  * Merge pull request #122 from hellomedia/master
  * Merge pull request #6 from Kunstmaan/fix-for-jenkins-screenshots
  * fix screenshot subcontext
  * add French translations
  * Merge pull request #5 from Kunstmaan/fix-for-jenkins-screenshots
  * fix to see screenshots appear in jenkins
  * add new line at end of file
  * add French translation
  * Merge pull request #239 from hellomedia/master
  * add French translation
  * Merge pull request #131 from jverdeyen/fix/entity_manager
  * Fix deprecated function calls
  * Merge pull request #99 from pmartelletti/master
  * Merge pull request #17 from pmartelletti/master
  * Merge pull request #102 from pmartelletti/master
  * Merge pull request #238 from pmartelletti/master
  * Merge pull request #4 from Kunstmaan/feature/test-hidden-field-values
  * support for hidden field value tests
  * Update messages.es.yml
  * Spanish translations
  * Spanish translations
  * Spanish Translation file
  * Create messages.es.yml
  * composer dependency fix
  * Merge pull request #130 from Kunstmaan/feature/fix-fixtures
  * creator service contexts removed
  * Merge pull request #129 from Kunstmaan/feature/remove-backstretch
  * remove backstretch - no longer in use
  * Merge pull request #120 from Kunstmaan/fix_duplicate_uploads_folder
  * fix dublicate uploads/media folder when using MediaCreatorService
  * Merge pull request #128 from h4cc/patch-1
  * Fixed Getting Started link.
  * Merge pull request #6 from ryandjurovich/develop
  * Update composer.json
  * Updated nelmio/alice
  * Merge pull request #237 from jverdeyen/feature/doctrinefixtures2.2
  * Fixed composer.json
  * Update composer.json
  * Merge pull request #236 from jverdeyen/feature/doctrinefixtures2.2
  * "doctrine/doctrine-fixtures-bundle": ">=2.1.0"
  * Merge pull request #118 from jverdeyen/feature/doctrinemigrations2.2
  * "doctrine/doctrine-fixtures-bundle": ">=2.1.0"
  * "doctrine/doctrine-fixtures-bundle": ">=2.1.*"
  * Update doctrine/doctrine-fixtures-bundle
  * Merge pull request #15 from skler/import-all-bundles-issue
  * Merge pull request #127 from Kunstmaan/feature/pagepart-table-names-plural-12315
  * Merge branch 'import-all-bundles-issue' of github.com:skler/KunstmaanTranslatorBundle into import-all-bundles-issue
  * [issue] Import all bundles translations
  * pageparts : plural table names (#12315)
  * Merge pull request #100 from skler/italian-translation
  * Added check_server_presence config option
  * Merge pull request #13 from Kunstmaan/fix_php_warning
  * use master composer dependencies
  * Merge branch 'master' into fix_php_warning
  * fix foreach php warning (fixes #11)
  * Merge pull request #85 from Kunstmaan/fix-default-export-12435
  * fixed param ordering...
  * Merge pull request #84 from Kunstmaan/fix-default-export-12435
  * fix for csv export - by convention paths (#12435)
  * Merge pull request #234 from skler/italian-translation
  * Correct comment
  * Merge pull request #13 from skler/italian-translation
  * Merge pull request #14 from skler/import-output
  * Italian Translation
  * Italian Translation
  * [issue] Import all bundles translations
  * Output the number of imported transaltion
  * Italian Translation
  * Merge pull request #97 from Kunstmaan/pp_form_type_service
  * allow pagepart FormType definition as string (service)
  * Merge pull request #99 from Kunstmaan/add_node_class_name
  * added node classname in source code
  * Merge pull request #125 from Kunstmaan/fix_layout_logo_path
  * Merge pull request #126 from Kunstmaan/better_default_prefix
  * better default table prefix for default site generator
  * fix logo url in header and footer
  * Merge pull request #124 from Kunstmaan/make_asset_path_dynamic
  * Merge pull request #77 from Kunstmaan/feature_less_restrictive_filter
  * Merge pull request #123 from Kunstmaan/html_language_dynamic
  * made asset path dynamic (fixes #118)
  * make html language dynamic (fixes #119)
  * Added some more steps to get a single language website
  * Merge pull request #122 from Kunstmaan/layout_generator
  * code improvements
  * fixed typo
  * solved merge conflict
  * refactored default site fixtures
  * refactored default site generator
  * Merge pull request #121 from Kunstmaan/feature/code-cleanup
  * added wrapper function to render a single file
  * layout is sub command of default site generator
  * removed layout templates from default site generator
  * move some twig template files to the layout generator
  * code cleanup : consistent use of string quotes...
  * move public asset files to layout generator
  * move grunt configuration file generation to layout generator
  * refactor ask bundle name
  * Merge pull request #96 from Kunstmaan/add_sub_entity_docs
  * added some formatting
  * added pagepart sub entity documentation
  * Add composer_root_version
  * Add composer_root_version
  * Added badges
  * Added badges
  * Added badges
  * Added some badges
  * Merge branch 'hotfix/fix_gruntfile_get_bundlename'
  * add get_bundlename on right places
  * Merge branch 'develop'
  * Merge pull request #116 from Kunstmaan/feature/fix_ie8_ie7_style_imports
  * fix imports in css.twig
  * Moved database create/update after bundle creation
  * Merge pull request #115 from Kunstmaan/change_datafixtures_path
  * moved datafixtures to separate sub directory to prevent conflicts
  * Merge pull request #233 from Kunstmaan/pagepart_sub_entities
  * Merge pull request #95 from Kunstmaan/pagepart_sub_entities
  * refactored pagepart sub forms
  * Merge pull request #24 from DracoBlue/master
  * allow deletion of pagepart sub entities
  * allow pageparts that have sub entities
  * Merge pull request #11 from Kunstmaan/fix/fixtures_unique_keys
  * Check for existing translations, before inserting fixtures
  * Merge pull request #6 from Kunstmaan/fix_travis
  * try to fix travis build error
  * Update README.md
  * Merge pull request #114 from Kunstmaan/locale_date_notation
  * show dates in the users locale
  * Create ADVANCED.md
  * Update README.md
  * Update GoingSingleLanguage.md
  * Update GoingSingleLanguage.md
  * Update GoingSingleLanguage.md
  * Create GoingSingleLanguage.md
  * Fix exception if you don't want to run the default site generator
  * Merge pull request #116 from Kunstmaan/fix_aviary_editor
  * fix aviary image editor
  * Merge pull request #5 from Kunstmaan/add_url_locale_guesser
  * remove unnecessary check
  * added url locale guesser
  * Merge pull request #1 from Kunstmaan/feature/translator
  * Update ManageTranslations.md
  * Merge pull request #113 from Kunstmaan/fix_dashboard
  * Fix composer install issue
  * Merge pull request #232 from lightupourworld/patch-1
  * Update README.md
  * updated default dashboard graphs
  * Merge pull request #112 from Kunstmaan/fix-small-issues
  * fix hide subnavigation
  * fix ios issues
  * Merge pull request #109 from Kunstmaan/fix_multiple_extends_tags
  * Merge pull request #111 from Kunstmaan/upgrade_to_bootstrap-3
  * fix columns homepage
  * fix navigation and splash
  * Update README.md
  * Update README.md with configuration info
  * Merge branch 'master' into feature/translator
  * KunstmaanTranslatorBundle configuration options explained
  * adjust css-bloc
  * upgrade css-block issue
  * upgrade css-block issue
  * Merge branch 'master' into upgrade_to_bootstrap-3
  * use grunt and livereload for scss
  * update vendor link
  * Merge pull request #110 from Kunstmaan/update_default_site
  * update article weight
  * Merge pull request #231 from Kunstmaan/add_version_check
  * solved merge conflicts
  * added new sattelite sub page
  * doormat and translation fixes
  * Updat README.md with default_bundle configuration information
  * small fixes
  * adjust submit button
  * update footer
  * fix slider
  * update slider img
  * fix sidemap
  * update
  * fix logo and fix social links
  * update search and language-nav
  * fix article page
  * update
  * use doctrine fixtures bundle 2.1.*
  * Update CreatingAnAdminList.md
  * Update README.md
  * Update README.md
  * Update AddingSearch.md
  * Update AddingArticles.md
  * Update CreatingAPage.md
  * Update CreatingAnAdminList.md
  * Update CreatingAPagePart.md
  * Update Installation.md
  * update slider
  * add fonts folder
  * update homepage
  * Merge branch 'update_default_site' into upgrade_to_bootstrap-3
  * fix: when you run the command multiple times, there are multiple extends tags added
  * cleanup default site generator fixtures
  * update
  * get logo back
  * Merge branch 'update_default_site' into upgrade_to_bootstrap-3
  * update homepage
  * Translator Bundle docs
  * website integration fixes
  * website integration fixes
  * higher weight on sitemap pages
  * start update layout
  * add webfont
  * update layout
  * update layout and homepage to bootstrap3 grid
  * remove collapse
  * Merge branch 'update_default_site' into upgrade_to_bootstrap-3
  * fix js footer
  * add search box in header when generating default site
  * added option to generate fixtures for the search generator
  * update scss layout
  * use Twitter bootstrap markup
  * toc updated
  * os x install updated
  * replace article templates when default site is generated
  * fix little issue grunt
  * extra page template for the homepage
  * add images on content pages
  * add articles / search
  * Merge branch 'update_default_site' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle into update_default_site
  * generate articles in different langauges + sort menu items in menu
  * translated article generator
  * only one form template layout
  * update homepage html
  * update homepage html
  * update to new html structure
  * updated form page
  * chapter fix
  * Links added...
  * Merge branch 'update_default_site' into update_default_site_frontend
  * update navigation
  * add sitemap page for other languages
  * doc update
  * show submenu in sidebar on content pages
  * Merge branch 'update_default_site' into update_default_site_frontend
  * Merge pull request #98 from Kunstmaan/fix_page_creator_title
  * make sure the translated title is used for the page entity
  * Merge pull request #115 from Kunstmaan/hotfix/fix_main_folder_not_always_one
  * Look for a first top folder where the parent is NULL
  * bugfixes admin list creation
  * create adminList pages and objects
  * added admin list page
  * update
  * update typo
  * fix wrong imports
  * fix type
  * update config
  * replaced content page
  * Merge pull request #8 from Kunstmaan/hotfix/minor_tweaks
  * Merge branch 'master' into hotfix/minor_tweaks
  * Merge pull request #7 from Kunstmaan/hotfix/disable_profile_add_and_edit
  * Flash message translations
  * Translator for the translator bundle
  * Change adminlist button styles, short text when way too long.
  * updated homepage content (en/nl)
  * create a page
  * Merge pull request #4 from Kunstmaan/locales_not_required
  * the default languagechooserlocales should not be required
  * added lange chooser
  * Disable add and edit button in backend.
  * Merge pull request #6 from Kunstmaan/hotfix/translations_and_routing
  * Added translations for some buttons and fixed the default locale when adding from the profiler
  * Merge pull request #3 from Kunstmaan/feature/compilerpass_configs
  * Updated readme with remark
  * - No need to include config file - No need to set languagechooserlocales as a parameter (included in bundle config) - Injecting languagechooserlocales with a Twig extension (to use as global)
  * Merge pull request #2 from Kunstmaan/fix_configs
  * added documentation
  * added twig global variable with languages
  * make bundle configuration better + require symfony < 2.4.0
  * Merge pull request #5 from Kunstmaan/feature/refactor-use-urls-from-configurator
  * Update view.html.twig
  * Added relative path for asset in image page part
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanBundlesDocs
  * pagepart docs : link to liip imagine docs added / admin list docs finished up...
  * Merge pull request #83 from Kunstmaan/fix/adminlist-templates
  * disable client side validation
  * Merge pull request #96 from Kunstmaan/fix_doctrine_cache_node_translations
  * when calling getChildNodes for the second time with an other language, the nodeTranslations are not updated
  * Merge pull request #81 from Kunstmaan/feature/fix-security-checks
  * add basic security checks in admin list controller actions / refactor template
  * Merge pull request #107 from Kunstmaan/fix/lowercase-routes
  * Merge pull request #230 from Kunstmaan/fix/actions-header
  * Merge pull request #94 from DracoBlue/patch-1
  * Update PagePartBundle.md
  * fix admin list generator routes
  * Merge pull request #79 from Kunstmaan/lowercase_getpathbyconvention
  * also display actions header if page not in menu
  * Merge pull request #106 from Kunstmaan/fix/adminlist-generator-sf-coding-conventions
  * fix - apply symfony coding conventions
  * use urls which are configured in the admin list configurator as much as possible
  * small symfony coding convention fix
  * Update Installation.md
  * Update Installation.md
  * Update Installation.md
  * documentation - work in progress
  * Merge pull request #105 from Kunstmaan/fix/pagepart-generator
  * fix for pagepart css class naming
  * fix for image pagepart naming
  * Merge pull request #97 from Kunstmaan/generate_article_lowercase_routes
  * Merge pull request #80 from Kunstmaan/fix_ambiguous_column_name
  * fix ambiguous column name error when counting the results
  * Update .travis.yml
  * Create .travis.yml
  * Create .travis.yml
  * Create .travis.yml
  * Initial commit
  * Merge pull request #102 from Kunstmaan/fix/article_menu_adaptor_service
  * Merge pull request #94 from Kunstmaan/feature/add_delete_option_role_admin
  * Merge pull request #104 from Kunstmaan/fix_homepage_slider
  * add the delete option for the role admin
  * added missing twig template
  * Merge pull request #103 from Kunstmaan/change_child_pages
  * Merge pull request #12 from Kunstmaan/fix/remove_overview_page
  * removed use statement
  * created method findActiveOverviewPages
  * automatically add the new page as child page of the existing pages
  * update page generator docs
  * update page generator docs
  * add entity name to menu adaptor service name
  * Merge pull request #96 from Kunstmaan/generate_bundle_lowercase_routes
  * Merge pull request #101 from Kunstmaan/fix/removed_entity_annotation
  * Merge pull request #100 from Kunstmaan/add_page_generator
  * removed double entity annotation
  * use symfony finder component for getting file lists from the filesystem
  * removed the unnecessary while loops around the askAndValidate functions
  * Merge pull request #74 from Kunstmaan/feature_allow_removal_of_distinct_count
  * removed space in beginning of file
  * Merge pull request #95 from Kunstmaan/extend_from_pageadmintype
  * call buildform from parent first
  * Merge pull request #99 from Kunstmaan/fix/throw_expectationexception
  * updated the documentation
  * Merge branch 'master' into add_page_generator
  * Merge pull request #93 from Kunstmaan/improve_section_display
  * Merge pull request #92 from Kunstmaan/fix_pp_section_config
  * bugfixes page generator
  * improve the way we show the section boxes on the admin interface (+modal)
  * bugfixes page generator
  * removed test code
  * removed test code
  * added page generator
  * move the admin type creating to a common place
  * moved the generate entity logic to a common place
  * make askForSections more dynamic and re-usable
  * added explanation to upgrade
  * throw expectationexception if publishbutton is null
  * lowercase the bundle name
  * better check for reserved field names used in parent class
  * move fields selection to common location
  * lowercase the routes in menuadaptor
  * lowercase the name in the controller annotations
  * remove spaces
  * use the lower function
  * move pagepart section selection to common class
  * lowercase route when generating bundle
  * mode askForBundleName to common place
  * refactored pagepart generator
  * extend from PageAdminType
  * Merge pull request #93 from Kunstmaan/add_single_column_homepage
  * changed pdf.pdf to smaller pdf
  * changed text in feature for offline page
  * fix: allow multiple pagepart configurations with the same context
  * wait before pressing button
  * changed filter method for firefox
  * removed empty lines
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * wait 1 second before creating subfolder
  * Merge pull request #94 from Kunstmaan/add_pp_fields_types
  * added new field types that can be used in the pagepart generator
  * Update AdminLoginLogout.feature
  * Fixed defaultlocale error
  * Fix dashboard page feature
  * DataFixtures
  * Added DoctrineFixtures
  * Depend on dev-master of KunstmaanAdminBundle
  * added missing single column homepage template
  * added single column homepage as option in default site generator
  * Delete InstallCommand.php
  * Update InstallCommand.php
  * Create InstallCommand.php
  * Don't hard depend on monolog, causes downgrades
  * Update NodeBundle.md
  * Create ConfigurableActionMenu.md
  * Update NodeBundle.md
  * Rename ProgrammatoryNodes.md to ProgrammaticallyCreateNodes.md
  * Create ProgrammatoryNodes.md
  * Update NodeBundle.md
  * Create ChainRouter.md
  * Update NodeBundle.md
  * Fix #4
  * Don't hardcode the alias in the filters.
  * Crucial setter :p
  * You can now set an AdminListConfigurator it to not prefix the countColumn with DISTINCT.
  * Delete AdminBundle.md
  * Create Permissions.md
  * Update CustomizingTheTopMenu.md
  * Delete CustomisingTheTopMenu.md
  * Create CustomizingTheTopMenu.md
  * Create CustomisingTheTopMenu.md
  * Merge pull request #93 from Kunstmaan/active_node_check_change
  * use same function to check if node is active than in adaptChildren
  * Update install instructions
  * Merge pull request #92 from Kunstmaan/feature/nodemenu-translations
  * fixed md syntax
  * fetch all languages in nodemenu to support translation links
  * composer.json
  * composer.json
  * Update composer.json
  * Update composer.json
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Merge pull request #89 from Kunstmaan/behat_background_login
  * Update .travis.yml
  * removed login
  * use background for admin login
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Merge pull request #88 from Kunstmaan/update_pagepartgenerator_docs
  * added table prefix documentation for pagepart generator
  * Update composer.json
  * Update composer.json
  * Added managed_locales to README
  * Merge branch '2.3' of github.com:Kunstmaan/KunstmaanTranslatorBundle into 2.3
  * Fix : [Fri Aug 09 10:19:02 2013] [error] [client 10.0.2.2] PHP Fatal error:  Call to a member function getIterator() on a non-object in /vagrant/vendor/kunstmaan/translator-bundle/Kunstmaan/TranslatorBundle/Component/HttpKernel/DataCollector/TranslatorDataCollector.php on line 35, referer: http://localhost:8003/
  * Merge branch '2.3' of git://github.com/Kunstmaan/KunstmaanTranslatorBundle into 2.3
  * Fix part of bug #3
  * Require doctrine migrations from main project Fix #2
  * Require dev voor doctrine migrations
  * Fix composer issues #2
  * Fix composer issues #2
  * Merge pull request #85 from Kunstmaan/feature_extract_analytics_js
  * Fix config file syntax #2
  * Fix AppKernel.php addition #2
  * Fix composer issues #2
  * Merge pull request #81 from Kunstmaan/feature/new_feature_detection_setup
  * Merge branch 'master' into 2.3
  * Fix issue #1
  * Merge pull request #87 from Kunstmaan/enable_pagination_style
  * Use ga_code as the variable for google_analytics_initialize
  * Merge pull request #59 from Kunstmaan/fix_form_pageparts_validation
  * Merge pull request #20 from Kunstmaan/feature_extract_analytics_js
  * enabled default bootstrap pagination style
  * Merge pull request #86 from Kunstmaan/fix_entitygenerator_columnnames
  * Fix snake_casing of columnnames for the EntityGenerator
  * Since the CIM keyword was removed, remove the tests as well.
  * Add dependency on doctrine migrations bundle
  * Add dependency on doctrine migrations bundle
  * Switch to Google Analytics script from the SeoBundle.
  * Updated composer.json
  * Updated composer.json
  * Extract analytics.js
  * Fix usage of undefined variable
  * tests fixed
  * validation of form pageparts & error messages refactored (fallback = default Symfony error messages)
  * Some more backwardscompatibility issues with the PagePartCreatorService.
  * CONTEXT_CONSOLE instead of CONTEXT_console
  * Merge pull request #82 from Kunstmaan/fix_behat_specificpage
  * Merge pull request #89 from Kunstmaan/feature_add_pagecreatorservice_documentation
  * use findAndClickButton for filter
  * Workflow examples
  * Merge pull request #18 from Kunstmaan/feature_generic_ga_script
  * Merge pull request #87 from Kunstmaan/feature_pagepartcreatorservice_doc_refactor
  * Merge pull request #113 from Kunstmaan/feature_document_mediacreatorservice_codingstandards
  * Merge pull request #3 from Kunstmaan/feature/disable-caching
  * Merge pull request #89 from Kunstmaan/place_properties_above_pagetemplates
  * Merge pull request #90 from Kunstmaan/docs
  * Merge pull request #91 from Kunstmaan/try-fix-travis
  * Merge pull request #66 from Kunstmaan/add_homepage_slider
  * Merge pull request #80 from Kunstmaan/rewrite_prefixes_bundles
  * Nicer copy for asking for the ArticleGenerator's entity name.
  * Remove the loop ik askForNamespace since it's already handled by askAndValidate
  * use glob() instead of scandir()
  * Handle variables a bit nicer in GenerateEntityCommand
  * Don't append extra underscore after prefix in PagePartGenerator
  * Only setOption in askForPrefix and askForNamespace if option available.
  * solved merge conflict
  * EntityGenerator Asks for a prefix & correctly sets tablename on the entity
  * Hardlink preview images
  * This is also needed for the no double underscores in pagepart generator
  * Show master build badge
  * Feedback for EntityGenerator as well.
  * Fix PHP 5.3 build
  * update scss links to bundle.GetName
  * Give feedback after every generator of what to do.
  * Revert "try fix travis build"
  * Update .travis.yml
  * Update PagePartBundle.md
  * try fix travis build
  * try fixing travis http://milesj.me/blog/topic/programming
  * fix for testing the bundle in travis, also other bundles does this
  * Update PagePartBundle.md
  * template chooser
  * Update PagePartBundle.md
  * Update PagePartBundle.md
  * Prefill the first found local bundle.
  * Update PagePartBundle.md
  * Update PagePartBundle.md
  * Start of documentation
  * updated to new feature dectection workflow
  * put the content of the properties tab above the pagetemplates, it already has this behaviour when using the default pagepart admin
  * Fixed Unit Tests
  * README fix
  * Preview image
  * README reviewed
  * README fix
  * Add profiler integration docs
  * Minor fixes
  * Edit/Add translation links in Translation Profiler(
  * Show translations in profiler
  * Merge pull request #88 from Kunstmaan/feature/slugify-header-and-toc
  * Merge pull request #5 from Kunstmaan/feature/twig-extension
  * removed is_safe html option
  * use slugify instead of url_encode for toc & header
  * exposed slugify as Twig filter
  * Minor bugfix + see where translations are used
  * Use generic logic for prefix & namespace for all generators where possible
  * Merge pull request #78 from Kunstmaan/fix/behat_errors
  * code cleanup
  * docs
  * created findAndClickButton method
  * added clean session and throw exceptions
  * fixed cleanPrefix, extracted askNamespace & askPrefix from the DefaultSiteGenerator
  * no double underscores in pagepartgenerator
  * Merge pull request #75 from Kunstmaan/add_doormap
  * Merge pull request #76 from Kunstmaan/feature/behat_should_see_with_or
  * function with or to check for text
  * changed passwords don't match text
  * changed has been deleted to is deleted
  * Updated readme
  * Merged
  * Updated README, added TODO
  * Minor tweaks
  * added basic style for doormat
  * Merge branch 'master' into add_doormap
  * Merge pull request #74 from Kunstmaan/behat_go_to_page
  * do not append a slash if it is singlelang
  * changed adminpage feature
  * fix for viewing created page in behat
  * Don't append underscore to empty prefix.
  * Add a new translation
  * Merge pull request #72 from Kunstmaan/fix_pagination_styling
  * re-added generateGruntFiles function call
  * Merge pull request #73 from Kunstmaan/fix_singlelanguage_defaultcontroller
  * Override DefaultController for single & multi-lang
  * Cleanup + added better README details
  * fixed global pagination styling to twitter bootstrap
  * Updated README
  * Removed validators
  * Merge pull request #67 from Kunstmaan/add_sitemap
  * Merge pull request #65 from Kunstmaan/add_breadcrumbs
  * Merge pull request #71 from Kunstmaan/fix/behat_multi-single_lang
  * Merge pull request #64 from Kunstmaan/fix/add_png_fallback_kunstmaan_logo
  * Merge pull request #70 from Kunstmaan/fix/adjust_to_new_cupcake_full_img_background
  * use check on multi
  * Merge pull request #62 from Kunstmaan/add_livereload_to_gruntfile
  * use parameter to determine multi-sinlge lang
  * added check for full img background
  * Merge pull request #69 from Kunstmaan/fix/pp_generator
  * Fixed tests
  * css naming convention
  * ask for db prefix
  * db naming convention
  * added slider styling
  * Merge branch 'master' into add_homepage_slider
  * added breadcrumb styling
  * Merge branch 'master' into add_breadcrumbs
  * refactored Pageparts to PageParts
  * Fix import and import forced + cache validator in non debug mode
  * refactored class names
  * Cache status fixed
  * added fallback image for kunstmaan logo
  * no-cache for JavaScript assets as well + small refactoring
  * Merge pull request #63 from Kunstmaan/artemis
  * fix _general.scss path
  * Removed @NotBlank
  * Merge pull request #225 from Kunstmaan/fix_translations
  * Merge pull request #2 from Kunstmaan/feature/disable-caching
  * Remove unneeded line.
  * Merge pull request #61 from Kunstmaan/artemis
  * Add livereload to the default Gruntfile.
  * add sitemap page
  * add general .scss
  * Fix DisableCacheListener.
  * Merge pull request #59 from Kunstmaan/feature/update_cupcake_navigation
  * Merge pull request #60 from Kunstmaan/feature/update_db_notifications
  * Add import
  * added information about updating db after generating article/news
  * fix typo
  * added new html and scss vars for updated cupcake navigation
  * correct indendation of spaces
  * added doormap with main site links
  * update articlegenerator
  * Merge pull request #224 from Kunstmaan/fix_translations
  * added twitter bootrstap classes
  * Merge with master
  * Merge pull request #88 from Kunstmaan/fix_translations
  * Merge pull request #112 from Kunstmaan/fix_translations
  * Merge pull request #58 from Kunstmaan/feature/add_fancybox_js
  * Use annotations for validation
  * added fancybox js link
  * correct use of function in askAndValidate
  * added breadcrumbs
  * Merge pull request #90 from Kunstmaan/pcs_hidden_from_nav
  * Merge pull request #56 from Kunstmaan/fix_hidden_menu_items
  * add option to hide the node from navigation when using the PageCreatorService
  * Merge pull request #57 from Kunstmaan/hide_articles_from_nav
  * hide the generated article from the navigation
  * hidden menu nodes/items should not be shown in the menu
  * Add wait when trying to login.
  * changed role_guest with is_authenticated_anonymously
  * Getting the menu right etc
  * Fix CodingStandards
  * Conform to codingstandards,  add documentation and put MediaCreatorService in the container.
  * Refactor PagePartCreatorService + doc + conform to CodingStandards
  * added homepage slider to default site generator
  * Merge pull request #55 from Kunstmaan/array_php53_compatible
  * make array syntax php 5.3 compatible
  * Improved CodingStandards
  * Merge pull request #221 from Kunstmaan/feature/drop_guest
  * Read all translatiosn files, after db
  * Merge pull request #49 from Kunstmaan/fix/entity_name_required
  * use askAndValidate
  * Cleaned up PageCreator & ACLPermissionCreatorService + docs.
  * Merge pull request #86 from Kunstmaan/feature/drop_guest
  * Merge pull request #58 from Kunstmaan/fix_translations
  * Merge pull request #10 from Kunstmaan/fix/index_node_translation
  * Merge pull request #57 from Kunstmaan/fix_required_label
  * Merge pull request #216 from Kunstmaan/fix/duplicate_role_name
  * removed blank line
  * removed die statement
  * changed syntax while loop
  * Edit/overview translations
  * Merge pull request #54 from Kunstmaan/feature_default_locale_listener
  * Merge pull request #53 from Kunstmaan/fix_prefixes
  * Merge pull request #52 from Kunstmaan/fix_kuma_logo
  * Merge pull request #50 from Kunstmaan/fix/searchpage_view
  * Merge pull request #45 from Kunstmaan/fix/uppercase_entity
  * Merge pull request #41 from Kunstmaan/feature/add-webfonts-comment
  * Merge pull request #40 from Kunstmaan/feature/add-fitvids
  * Merge pull request #31 from Kunstmaan/feature/drop_guest
  * Change symfony requirements
  * Change symfony requirements
  * Remove the default controller action and add a DefaultLocaleListener that picks up unsuccesful root requests and redirects to the defaultlocale's root page.
  * Showing translations in admin, refactoring continues..
  * Make sure the prefixes contain just a single underscore.
  * Merge pull request #51 from Kunstmaan/add_article_generator_data
  * Correctly generate the asset path for the kuma logo
  * Update translations
  * Update translations
  * Update translations
  * Update translations
  * option added to generate data fixtures when running the article generator command
  * fix typo
  * Added information for installing elasticsearch
  * fix link to documentation
  * use correct method call
  * Merge pull request #87 from Kunstmaan/add_pagecreation_option
  * add creator option for PageCreatorService
  * check for active scope
  * Fix link for further documentation
  * prevent empty entity name
  * Merge pull request #46 from Kunstmaan/fix/escape_entities
  * Merge pull request #47 from Kunstmaan/fix/append_services
  * Merge pull request #48 from Kunstmaan/fix/singlelang_multilang
  * singlelang and multilang support
  * append services.yml correctly
  * escape entity name
  * Merge pull request #44 from Kunstmaan/show_article_author
  * uppercase first letter entity
  * show author dropdown when adding/editing article
  * Merge pull request #223 from Kunstmaan/fix/issue74PagePartBundle
  * Fetching translations form db, if not from file, if not.. show keyword
  * update styling for input-append in span3 (banner)
  * Merge pull request #43 from Kunstmaan/add_pagepart_generator_doc
  * added pagepart generator documentation
  * Merge pull request #72 from Kunstmaan/fix/elseif_space
  * fix space
  * Merge pull request #64 from Kunstmaan/fix_controlslayout
  * Merge pull request #86 from Kunstmaan/fix_10205_drag_pp_to_empty_region
  * fix issue canExport true and canAdd is false
  * Merge pull request #42 from Kunstmaan/fix/admintests
  * generate admin tests in one central plcace
  * Merge pull request #222 from Kunstmaan/fix/issue193_new_user_autocomplete
  * added autocomplete off for addUser form
  * fix sidebar tree
  * When implementing FormAdaptorInterface's adaptForm interface we should also pass the required option to the formBuilder
  * Remove unneeded index.html.twig
  * added webfonts comment
  * Pull out conversion logic + deduplicate the orderitems before rendering the template
  * Cleanup google Analytics init script
  * Provide documentation and tweak twig functionnames
  * Split out Google Analytics helpers + add ecommerce tracking helper
  * Add twig function to initialize Google Analytics script.
  * import only needed validation
  * added fitvids link
  * removed unused use lines
  * use $ jquery shortcut + remove another console.log
  * use annotations for validation of fields
  * Merge pull request #39 from Kunstmaan/feature/add-socialite
  * added default socialite integration
  * Merge pull request #10 from Kunstmaan/fix/overviewPageDate
  * changed image.jpg to image.png
  * add variables for navigation-item-link
  * fix readdir
  * display bundle last update date
  * added /Context for the admintests generate
  * Merge pull request #35 from Kunstmaan/feature/behat_backend_tests
  * loop over dir to copy behat tests
  * Adjust vars image folders to new names
  * added documentation for version check
  * Typo's paths scss
  * add project name in version check
  * only do version check every 24 hours
  * added parameter to disable version check
  * added Media folder for behat tests
  * Merge branch 'master' into add_version_check
  * Only expand a dropzone when it's possible to drop the pagepart in the empty dropzone.
  * Allow dragging of pageparts from 1 region to an empty retion/
  * use suppresswarnings(unused)
  * added phpmd supresswarning for unused local var
  * removed unnused local variable
  * changed import navbar to navigation
  * fixed date tag in twig
  * Fixed typo
  * added the date of the article in the article overview
  * place ga script in comment because this will not work
  * Merge pull request #34 from Kunstmaan/fix/generate_assets
  * removed # from testing
  * copy the entire dir for the assets and not file per file
  * Merge pull request #9 from Kunstmaan/fix/headerArticlePage
  * changed the articlepage header to a h1
  * Merge pull request #32 from Kunstmaan/fix/gitignore
  * Merge pull request #33 from Kunstmaan/artemis
  * full-img-bg config
  * Merge pull request #7 from Kunstmaan/feature/articleOverviewClass
  * added classes to the ul and li's
  * the .gitignore file is used in the generator
  * Fix errors when called without an AbstractPage object
  * use double backslash in the generate:article
  * removed test
  * Merge branch 'master' into feature/drop_guest
  * fixed typo
  * sanity check added (just in case)
  * modify ROLE_GUEST acl security identity
  * modified fixture / removed GuestUserListener entry in doc
  * Merge pull request #85 from Kunstmaan/2.2-pagepartcreatorservice
  * Merge pull request #29 from Kunstmaan/add_pagepart_generator
  * Merge pull request #85 from Kunstmaan/fix_publish_later
  * Merge pull request #13 from Kunstmaan/extend_seo_title_master
  * Merge pull request #15 from Kunstmaan/feature/remove_cimkeyword
  * cookie consent variables added from cupcake
  * cim keyword removed (Roadmap #104)
  * add listener to set a caching header
  * security.yml change documented...
  * remove ROLE_GUEST & guest user...
  * Merge branch 'master' into feature/drop_guest
  * remove ROLE_GUEST & guest user...
  * Merge branch 'master' into feature/drop_guest
  * remove ROLE_GUEST & guest user...
  * Oops, forgot the user
  * Merge pull request #217 from Kunstmaan/fix/is_group_valid
  * analitics
  * Merge branch 'extend_seo_title'
  * Added extra function to get the SEO title or a value you provide. + Added usage in README
  * add nav classes for cupcake nav
  * Merge pull request #9 from Kunstmaan/fix/request_scope
  * Merge pull request #110 from Kunstmaan/feature/media_folder_delete
  * Use given user for createPublicVersion in NodeAdminPublisher
  * script syntax
  * Merge account for APi changes in createPublicVersion
  * Invert publish later bug
  * Merged allow publish later for drafts
  * Merge pull request #219 from Kunstmaan/fix_timepicker
  * Fix unschedulePublish
  * Merge pull request #30 from DracoBlue/master
  * Update README.md
  * update README file
  * implement script injector service which injects the livereload script snippet into your html
  * better defaults for timepicker
  * Fix timepicker to correctly use arrows.
  * change iconfont setup
  * add default folders and files and change base.scss
  * initial setup
  * add createdby to footer
  * remove cupcake prefix from navigation
  * favicon and apple touch icon paths in layout
  * Fixed code blocks in README.md
  * Merge pull request #111 from Kunstmaan/feature_mediacreatorservice
  * Correct checks for getClientOriginalName
  * Add MediaCreatorService.
  * Updated packages, building the form
  * added use elementnotfoundexception
  * added forgot password + cleanup and fixes
  * Attemt to fix travis
  * Exporting translations into files (not yet finished)
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * move grunt back to root
  * fixed version twig template
  * filter improvement
  * first version kunstmaan bundle version check
  * Updated README.md
  * added additionally filter option
  * Add force import in backend
  * Updated README with basic features
  * - Add translation from the backend interface - Reset all translation and translation domain flags from a console command - Reset translation cache form console command - Updated readme with latest commands and options - Added validator for new translations (added from the backend) (needs some work) - Updated backend controller
  * Attemt to fix travis
  * Attempt to fix travis
  * removed getMainContext from the filter method
  * added filter test
  * Changes in controller
  * Updated README with 2 new commands
  * code cleanup with phpcs
  * Request cache status command
  * flush cache from command
  * added test for media delete
  * prevent deleteion of the Media folder
  * added page testing
  * Fix travis build
  * minor README changes
  * README updates
  * Fix: mapped fields in migrations sql
  * update defaultsitegenerator
  * update configs and view navigation
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanSitemapBundle
  * don't load offline pages
  * Migrations
  * Checkbox css fix
  * check for scope before creating new
  * Merge pull request #109 from Kunstmaan/fix/upload_in_selectbox
  * Merge branch 'fix/upload_in_selectbox' of git://github.com/Kunstmaan/KunstmaanMediaBundle into fix/upload_in_selectbox
  * fixed coding standards
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * update comments scss and include cupcake navigation
  * cleanup uses
  * fix bulk-upload, FormView::set() has been removed
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * add gitignore in Resources in WebsiteBundle
  * Update _z-index.scss
  * Update _typography.scss
  * Update _tooltips-popover.scss
  * Update _tables.scss
  * Update _paths.scss
  * Update _pagination.scss
  * Update _navbar.scss
  * Update _hero-unit.scss
  * Update _grid.scss
  * Update _forms.scss
  * Update _dropdowns.scss
  * Update _colors.scss
  * Update _buttons.scss
  * Update _bootstrap-imports.scss
  * Update _base.scss
  * Update _base.scss
  * Css fix upload modal windows
  * Upload modal fix
  * create dir before travis build
  * Add forgotten files
  * Refactored commandHandler + creating exporter command
  * Possible to disable bundle
  * Cache Validator + backend check
  * update header
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * adjusted header
  * added test for media
  * fixtures : add checkbox pagepart
  * Merge branch 'fix/upload_in_selectbox' of git://github.com/Kunstmaan/KunstmaanMediaBundle into fix/upload_in_selectbox
  * fixed modal by adding modal body
  * add media buttons when choosing media
  * fix choice pagepart
  * Develop branch badge from Travis CI
  * fix documentation, we work with a string, now with an array for choices
  * Fixing travis builds
  * fixtures : generate ChoicePagePart and EmailPagePart in form
  * Updated README
  * Initial commit
  * Comments, fixes, ..
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * VideoPagePart fixture
  * Sublime project file
  * rewrite form html
  * Dont add empty translations.
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * add styles page
  * PrePersist/PreUpdate fix
  * Update translations from backend - no validation yet
  * Ignore phpunit logs
  * More unit tests
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * rewrite html
  * DownloadPagePart fixture
  * Test + clover test report
  * add fixture references
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * extended fixtures
  * fix typo
  * update html
  * added files to generate command
  * adminsettings tests, using subcontexts
  * Better controllers and flush cache files
  * allow multiple fields with the same type
  * changed method addViolationAtSubPath into  addViolationAt
  * let the form logic generate the label
  * Read out all bundles' translations form files
  * No cached catalogues when running debug=true
  * Merge branch 'artemis'
  * new line
  * update form html
  * update form html layout
  * Writing translation resources into cache file (when debug is off)
  * render package.json
  * adjustments to twitter bootstrap html
  * Merge branch 'master' into artemis
  * copy fields.html.twig
  * Loading translations from database, and already caching them in default php message catalogue
  * update html to bootstrap fluid-grid system
  * add constraint for unique role name
  * Register database loader as translator loader
  * Merge pull request #28 from Kunstmaan/artemis
  * fix paths
  * artemis updates
  * generate Grunt files
  * Artemis: Render new assets
  * Artemis: Render new assets
  * fix path
  * added rich text + single reference + multi reference
  * Using TranslationGroupManager to make the entities more abstract
  * Revert changes
  * Updated composer
  * first version of the pagepart generator
  * Add PagePartCreatorService.
  * Merge pull request #24 from Kunstmaan/fix_analyzer_languages
  * changed language configuration format
  * Updated composer
  * Use @dev
  * Updated misleading command options/arguments
  * Merge pull request #83 from Kunstmaan/fix_create_page_template_configuration
  * also fixed it in the twig extension
  * Importer van files ok.
  * fix for when we automatically need to create a new PageTemplateConfiguration
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * several more fixes after merge
  * merge fix
  * merge fix
  * Merge branch 'symfony-2.3'
  * "symfony-cmf/routing-bundle": "*", since it's still in dev
  * add loaders with a compiler pass
  * Merge pull request #82 from Kunstmaan/optimize_admin_node_tree
  * Merge pull request #81 from Kunstmaan/configuration
  * Startup van import command handler
  * Merge pull request #81 from Kunstmaan/inheritance_section_pageparts
  * Update composer.json
  * fix imports
  * Merge pull request #4 from Kunstmaan/symfony-2.3
  * Merge pull request #11 from Kunstmaan/symfony-2.3
  * remove config.yml
  * Merge branch 'master' into symfony-2.3
  * Merge pull request #54 from Kunstmaan/fix-choicesubmissionfield-required
  * Removed Resources/config/config.yml
  * Merge with master and changed var cmf_routing
  * Merge pull request #215 from Kunstmaan/fix/edit_user_no_require_password
  * Merge pull request #107 from Kunstmaan/fix_originalname_checks
  * Merge pull request #10 from Kunstmaan/configuration
  * Merge pull request #70 from Kunstmaan/configuration
  * Merge pull request #213 from Kunstmaan/configuration
  * Merge pull request #22 from Kunstmaan/configuration
  * Merge pull request #104 from Kunstmaan/configuration
  * Merge pull request #55 from Kunstmaan/configuration
  * Use correct FileHandler class in MediaManager
  * Correct checks for getClientOriginalName
  * password not blank validation only on Registration
  * Entities and repositories
  * Add translations top menu item
  * Initial commit
  * remove debugging code
  * Merge pull request #8 from Kunstmaan/symfony-2.3
  * Merge pull request #82 from Kunstmaan/symfony-2.3
  * Merge pull request #23 from Kunstmaan/symfony-2.3
  * Merge pull request #12 from Kunstmaan/symfony-2.3
  * Merge pull request #3 from Kunstmaan/symfony-2.3
  * Merge pull request #6 from Kunstmaan/symfony-2.3
  * merge fix
  * Merge pull request #83 from Kunstmaan/less_frontend_queries
  * Merge pull request #9 from nchaulet/symfony-2.3
  * updated doc
  * updated doc
  * Merge pull request #214 from Kunstmaan/symfony-2.3
  * Merge pull request #71 from Kunstmaan/symfony-2.3
  * updated doc
  * Merge pull request #3 from Kunstmaan/symfony-2.3
  * Merge pull request #3 from Kunstmaan/symfony-2.3
  * Merge pull request #56 from Kunstmaan/symfony-2.3
  * Merge pull request #105 from Kunstmaan/symfony-2.3
  * updated doc
  * Merge pull request #23 from Kunstmaan/symfony-2.3
  * Merge pull request #84 from Kunstmaan/symfony-2.3
  * updated doc
  * update doc
  * Merge branch 'symfony-2.3' of github.com:Kunstmaan/KunstmaanMediaBundle into symfony-2.3
  * update doc
  * Merge branch 'symfony-2.3' of github.com:Kunstmaan/KunstmaanGeneratorBundle into symfony-2.3
  * updated doc
  * fix
  * updated doc
  * support inheritance from multiple partent configuration files
  * updated doc
  * updated doc
  * updated doc
  * added inheritance option for configuring the available section pageparts
  * solved slash replace problem
  * fix article generation : template ArticlePage
  * fix admintests generator
  * $form->getAttribute is deprecated, use $form->getConfig()->getAttribute()
  * fix deprecated methods
  * getFlashes() is deprecated. Use the FlashBag instead.
  * FOSUserBundle dev-master
  * "friendsofsymfony/user-bundle": "1.3.2"
  * less queries for rendering frontend menu's
  * "symfony-cmf/routing-bundle": "1.1.0-beta1@dev"
  * behat login logout tests
  * fixed modal by adding modal body
  * trying to resolve depenency issues
  * Merge branch 'master' into symfony-2.3
  * "symfony-cmf/routing-bundle": "1.1.*@dev"
  * fallback locale not needed anymore
  * Add travis ci badge
  * fixed merge conflicts
  * Removing the need to import config.yml into you app/config/config.yml
  * paramters should be set at the prepend function
  * Removing the need to import config.yml into you app/config/config.yml
  * Updated README.md
  * Removing the need to import config.yml into you app/config/config.yml
  * Removing the need to import config.yml into you app/config/config.yml
  * getEntityManager -> getManager
  * fix cmf_routing
  * Removing the need to import config.yml into you app/config/config.yml
  * possibility to add voting_default_value in parameters.yml (again)
  * Removing the need to import config.yml into you app/config/config.yml
  * Merge pull request #80 from Kunstmaan/publish-later
  * Merge branch 'master' into configuration
  * Add bootstrap and tests
  * Config without config.yml import needed
  * bugfixes publish later
  * use SensioFrameworkExtraBundle version 2.3.*
  * Merge branch 'symfony-2.3' of git://github.com/Kunstmaan/KunstmaanNodeBundle into symfony-2.3
  * fix version typo
  * Update composer.json
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * Merge branch 'master' into symfony-2.3
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * use self.version for Kunstmaan Bundles
  * Symfony 2.2 dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Merge branch '2.2' of git://github.com/Kunstmaan/KunstmaanNodeBundle into 2.2
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * Symfony 2.2 and Kunstmaan bundles 2.2.8.* dependency
  * merge cleanup fix
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * merge with master
  * Update RepositoryResolver.php
  * add more test
  * add units test
  * add documentation
  * update for add securty
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * Merge branch 'sensio-2.2' into symfony-2.3
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * change to Symfony >=2.3.0,<2.4.0
  * update composer to Symfony 2.3. Switch from symfony-cmf/routing-extra-bundle to symfony-cmf/routing-bundle
  * setup for symfony 2.3 update
  * Update composer.json
  * Update composer.json
  * composer update
  * composer update
  * do only one query to generate the node tree
  * switch to fuzzines 0.7 for a bit more improved searching
  * use the analyzer field to choose a languange analyzer
  * use a match query with fuzzines of 0.7
  * Merge pull request #69 from Kunstmaan/feature/make-em-accessible-in-configuration
  * make the em accessible in the admin list configuration
  * Merge pull request #209 from Kunstmaan/feature/show_topmenu_in_sidebar_configurable
  * Merge pull request #212 from Kunstmaan/fix/overflow_media_popup
  * overview tweaks
  * set limit and offset to default null
  * fix import
  * fix service.yml to accomodate change to hide articles in tree
  * hide articles from the tree menu
  * Merge pull request #5 from kolah/patch-1
  * Merge pull request #77 from Kunstmaan/2.2-add_find_by_internal_name
  * fix overflow media popup
  * Update CloneListener.php
  * add media buttons when choosing media
  * Multilanguage site + locale
  * Merge pull request #73 from Kunstmaan/fix/multilanguage_default_locale
  * Make it configurable to show the topmenu in sidebar
  * Merge pull request #211 from Kunstmaan/feature/2.2-permission-creator-service
  * fix load in locale
  * add missing offset/limit code
  * Merge branch 'expanding-articles'
  * fix no services.yml anymore
  * ArticleOverviewPage repo fixes
  * article generation improvements
  * article improvements
  * article changes based on recent project
  * Merge pull request #47 from Kunstmaan/fix_submission_list
  * making form submission list PHP 5.3 compatible
  * add force option to the permission creator service
  * Update MediaManager.php
  * Require correct FileHandler class
  * add required to the ChoiceFormSubmissionType
  * Merge pull request #53 from Kunstmaan/fix_tostring
  * Add new function to easily fetch a nodetranslation by language & internal name
  * fix toString method should always return a string
  * Add permission creator service
  * Merge pull request #2 from Kunstmaan/performance_change
  * Merge pull request #75 from Kunstmaan/fix_slash
  * better fix
  * Add / to preview url
  * performance improvement by not loading the page from database, warning, I removed the structured node
  * Merge pull request #52 from Kunstmaan/fix-choice-required
  * fix choice field : required
  * Merge pull request #74 from Kunstmaan/2.2-pagecreator_updates
  * Fix some logic issues in PageCreatorService
  * use SEO page title
  * fix tests
  * fix NodeBundle tests (cherry picked from commit b92a9ceb676d8cdec27b8d2e28770f3f55eafa47)
  * slugifier moved to UtilitiesBundle (cherry picked from commit 7484db5c60ca26dc157031dd1eabcebb786e2893)
  * slugifier moved to UtilitiesBundle
  * fix NodeBundle tests
  * fix php code sniffer line exceeds 120 characters
  * fix php code sniffer issues
  * Make it configurable to show the topmenu in sidebar
  * Quick fix to make cookie secure (cfr security audit)
  * Merge pull request #207 from Kunstmaan/fix/inputs_span3
  * temp fix inputs span3
  * fix: don't use array as param
  * Merge pull request #51 from Kunstmaan/fix/choicefieldvalidation
  * fix typo
  * Fix depreciation message from FOSUserBundle
  * fix choice field validation
  * Merge pull request #78 from darles/master
  * getDefaultOptions() is deprecated since version 2.1 and will be removed in 2.3. Use setDefaultOptions() instead.
  * getDefaultOptions() is deprecated since version 2.1 and will be removed in 2.3. Use setDefaultOptions() instead.
  * getDefaultOptions() is deprecated since version 2.1 and will be removed in 2.3. Use setDefaultOptions() instead.
  * Add logging : log document info when indexing
  * Added the locale to the search, only retrieve pages from the correct language
  * Fix when locale is invalid after first guess
  * Update widget.html.twig
  * Multilanguage site + locale
  * add locale to the request
  * gracefully handle the DocumentMissingException
  * remove var_dumps used to test
  * index children even if the page itself should not be indexed (i.e. structured nodes)
  * HasCustomSearchType interface to override indexed object type
  * Fix include
  * Merge pull request #204 from Kunstmaan/make_it_possible_to_not_load_child_menu_items
  * Update MenuItem.php
  * make_it_possible_to_not_load_child_menu_items
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Revert "Update composer.json"
  * Merge pull request #71 from Kunstmaan/fix_autosave
  * fix autosave
  * Merge pull request #68 from webscriptsolutions/patch-1
  * Update composer.json
  * Use built-in _format to enable xml output
  * Merge pull request #70 from Kunstmaan/fix_getPage
  * Use getPublicNodeVersion
  * Merge pull request #50 from Kunstmaan/fix_unique_id
  * let's work with standard analyzer and fuzzy matching until we find a more permanent solution
  * Fix invalid ID names
  * code formatting php-cs-fixer
  * add mode to controller route
  * fix to use xml.twig
  * sitemap page
  * Merge pull request #76 from Kunstmaan/fix_ckeditor_pageparts
  * Merge pull request #75 from Kunstmaan/fix_clone_doublepageparts
  * Merge pull request #69 from Kunstmaan/fix_autosave
  * first commit
  * pageparts were cloned twice on deepclone when there are multiple ppconfigurations for the same context
  * fix pagepart editor when adding a pagepart
  * fix auto saving new version, version list ordering and editing a draft item was broken
  * Merge pull request #68 from Kunstmaan/include-offline-param
  * added docs
  * added includeOffline parameter to allow to retrieve offline nodes (StructureNode)
  * Rewrite AdminBundle tests
  * Use correct file for AdminSettingsFeature
  * Fix DefaultSiteGenerator to generate the files correctly and in the correct places
  * Add a default index action to the DefaultController in the Bundle generator.
  * changes according to changes in the NodeSearchBundle
  * perform search in constructor to allow a response to be available to be able to have the facets before the paginated search results
  * Update layout.html.twig
  * Merge pull request #73 from Kunstmaan/fix/pagepartsadmin_small_area
  * Merge pull request #199 from Kunstmaan/fix/pagepartsadmin_small_area
  * fix pagepart admin layout for smaller screens
  * fix pagepart admin layout for smaller screens
  * Merge pull request #67 from Kunstmaan/fix/filters_ie9
  * Merge pull request #198 from Kunstmaan/fix/filters_ie9
  * change classname removeFilter to removeThisFilter for ie9
  * change classname removeFilter to removeThisFilter for ie9
  * Merge pull request #197 from Kunstmaan/fix/width_no_sidebar
  * added css for fix width when no sidebar is present
  * Merge pull request #195 from Kunstmaan/user_unique_validations
  * Merge pull request #196 from Kunstmaan/feature/update_ui
  * fix and update anim sidebar
  * adjust sidebar
  * reset arrow and adjust styling adjust_sidebar
  * update style and remove ugly blue
  * better approach for validations
  * make username and email unique validation
  * Quickfix slug reset
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanNodeBundle
  * Merge branch 'unique_slug_foreach_lang'
  * Merge pull request #63 from Kunstmaan/show_untranslated_pages_in_tree
  * Merge pull request #65 from Kunstmaan/unique_slug_foreach_lang
  * Merge pull request #66 from Kunstmaan/reset_slug_button
  * work with the new Sherlock RawRequest
  * work with the new sherlock RawRequest
  * unneeded space
  * reset_slug_button
  * unique_slug_foreach_lang
  * show untranslated nodes in tree
  * Merge pull request #192 from Kunstmaan/feature/update_ui
  * fix issue tree and background container
  * typo fix
  * sherlock dev-master
  * sherlock dev-master
  * fix
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanNodeSearchBundle
  * fix for PHP 5.3
  * Merge pull request #7 from Kunstmaan/fix/php53
  * Fix compatibility break with PHP 5.3
  * update ui
  * Merge branch '2.2' of github.com:Kunstmaan/KunstmaanAdminListBundle into 2.2
  * fix dbal export
  * Merge pull request #66 from Kunstmaan/fix/sorttable
  * Merge pull request #191 from Kunstmaan/fix/sorttable
  * added css for sortable styling
  * added adjusted html and functionality for sortable styling
  * cmf routing went to 1.0.0 tag, and the branch 1.0 is removed
  * cmf routing went to 1.0.0 tag, and the branch 1.0 is removed
  * fixes
  * don't break adminlist generator if a type to filtertype mapping doesn't exist
  * Don't use CG GeneratorUtils
  * documentation update
  * work with dev-master
  * fix basic generators (bundle, adminlist, default-size, entity, admin-tests)
  * cleanup and tweaks
  * article generation update
  * changes to Author controller
  * remove unused code
  * fix
  * generate article author + article generator updates
  * Article author and article fixes/updates
  * Merge pull request #190 from Kunstmaan/fix_form_layout
  * put content tab first
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanPagePartBundle
  * fix form layout, using input_prop class, we use this on other locations too and its not too small, the media field also uses this width
  * Merge pull request #11 from Kunstmaan/fix-9188
  * don't show social widgets when there is no URL or linkedin product id.
  * include facebook JS for like widget
  * Update script.js
  * Typo
  * fix for pagetemplate update
  * Fix after merge: missing request
  * fix missing request
  * fix for Page templates update
  * add Request parameter
  * for dependency for symfony 2.2
  * "sensio/generator-bundle": "2.2.*"
  * Merge pull request #20 from Kunstmaan/sherlock
  * Merge branch 'master' into sherlock
  * Merge pull request #10 from Kunstmaan/pagetemplates
  * Merge pull request #3 from Kunstmaan/pagetemplates
  * Merge pull request #69 from Kunstmaan/pagetemplates
  * Merge branch 'master' into pagetemplates
  * Merge pull request #58 from Kunstmaan/pagetemplates
  * Merge branch 'master' into pagetemplates
  * Merge pull request #60 from Kunstmaan/publish-later
  * Merge pull request #21 from Kunstmaan/pagetemplates
  * Merge pull request #188 from Kunstmaan/publish-later
  * Merge pull request #183 from Kunstmaan/pagetemplates
  * Remove unneeded files
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanLanguageChooserBundle
  * Initial commit
  * Initial commit
  * remove taggable dependency
  * getOverviewPage refactor
  * refactoring getOverviewPage + documentation
  * article generation documentation
  * php-cs-fix + article generation update
  * php-cs-fixer
  * ArticlePage  template, PagerFanta dependency, more refactoring
  * page template
  * more article tweaks and updates
  * various fixes and tweaks
  * tweaks
  * dependency on the dev-master of Sensio/GeneratorBundle (GeneratorCommand)
  * publish later
  * latest version bootstrap-datepicker
  * fix compatibility FOSUSerBundle 1.3.1
  * generator 2.2 changes default-site
  * FOSUserBundle 1.3.1
  * tweaks
  * various updates after generator coding
  * Article generation
  * set $em to protected add refactored getQueryBuilder method to overwrite creation of the QueryBuilder
  * AdminList add, edit and delete
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanArticleBundle
  * php-cs-fix code formatting
  * typo fix in readme
  * first commit
  * fix moved template
  * Searchpage generator docs
  * SearchPage generator
  * fixed bundle generator
  * SearchPage Generator
  * tweak
  * check for facets
  * Merge pull request #98 from kolah/patch-1
  * Added missing use statement in Helper\MediaManager
  * refactoring according to changes in Sensio/GeneratorBundle 2.2
  * coding style remarks Wim
  * Doc remarks from Wim
  * Update TabPane.php
  * update to work with the Sensio/GeneratorBundle 2.2
  * Merge pull request #184 from Kunstmaan/fix_editgroup_messages
  * Fix group and role flashbag messages
  * remarks on pull request
  * code cleanup
  * code cleanup
  * code cleanup
  * code cleanup
  * code cleanup
  * code cleanup
  * Merge branch 'master' into pagetemplates
  * updated renamed interface
  * Renamed the interface to something more sensible
  * Merge pull request #174 from Kunstmaan/validation-refactor
  * Merge branch 'master' into pagetemplates
  * pagetemplates
  * pagetemplates
  * fix deleteIndex
  * fix default max per page
  * method name changes
  * renamed several methods for better understanding
  * rename method document to addDocument
  * moved response doc a bit higher
  * a bit more doc on the response
  * Merge pull request #19 from Kunstmaan/missingformpps
  * Merge pull request #57 from Kunstmaan/disable_filter_adminlist_created_and_updated
  * Make the SearchPage abstract so it can be extended
  * filtering on created and updated not working, so disabled at this moment
  * Merge pull request #56 from Kunstmaan/disable_order_adminlist_created_and_updated
  * disable sorting of the created and updated column
  * added more documentation
  * additional documentation
  * License
  * removed obsolete template, moved to NodeSearchBundle
  * moved dependeccies that are not related to the GeneratorBundle to suggested packages. They're dependencies of the generated classes
  * php-cs-fixer fix
  * template uses PagerFanta too now
  * Added pagination using PagerFanta
  * fix size pagination parameter
  * Merge pull request #3 from kolah/patch-1
  * Updated Tagging entity
  * implementation of the checkbox pagepart
  * Merge pull request #48 from Kunstmaan/checkboxpagepart
  * implementation of the checkbox pagepart
  * pagination
  * work with the new SearchBundle config
  * improved analyzer languages config
  * use analyzer field for language analyzer selection
  * analyzer languages
  * doc update
  * typo fix
  * Code formatting and cleanup with php-cs-fixer
  * Code formatting with php-cs-fixer
  * added IndexNodeEventListener (KunstmaanSearchBundle), will index tags for Nodes when they implement Taggable
  * removed Taggable dependency, give $doc by reference to event, fixed IndexNodeEvent
  * delete and index children of the updated node
  * Instead of using an interface to add addition content, you can now use an EventListener to manipulate the document
  * pagetemplates
  * pagetemplates
  * pagetemplates
  * pagetemplates
  * pagetemplates
  * added missing form pageparts
  * Merge pull request #2 from Kunstmaan/master
  * Merge remote-tracking branch 'origin/2.2'
  * symfony >=2.1.0,<2.3.0
  * Merge pull request #178 from Kunstmaan/master
  * symfony/symfony >=2.1.0,<2.3.0
  * fix for dependencies in older projects
  * Merge pull request #2 from Kunstmaan/feature/symf22
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * fix
  * fix
  * tweaks, updates, refactoring
  * provider refactoring
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge pull request #21 from Kunstmaan/feature/symf22
  * Merge pull request #18 from Kunstmaan/feature/symf22
  * Merge pull request #46 from Kunstmaan/feature/symf22
  * Merge pull request #9 from Kunstmaan/feature/symf22
  * Merge pull request #68 from Kunstmaan/feature/symf22
  * Merge pull request #96 from Kunstmaan/feature/symf22
  * Merge pull request #65 from Kunstmaan/feature/symf22
  * Merge pull request #54 from Kunstmaan/feature/symf22
  * Merge pull request #177 from Kunstmaan/feature/symf22
  * Merge pull request #16 from Kunstmaan/feature/symf22
  * template override
  * fixes after copy
  * HasCustomSearchContent interface
  * Documentation
  * first commit
  * remove KunstmaanNodeBundle dependency, moved to another bundle
  * Merge branch 'sherlock' of git://github.com/Kunstmaan/KunstmaanSearchBundle into sherlock
  * update and delete document
  * fix
  * doc update
  * Doc tweaks
  * added commands to doc
  * doc updates
  * code formatting
  * documentation
  * Rename to SherlockSearchProvider
  * move Node related files to Node folder, add indexnameprefix, some doc, ...
  * search refactoring
  * code formatting with php-cs-fixer
  * uid parameter when index document, allows for overwriting documents
  * documentation
  * fixes, tweaks, refactoring
  * move from Service to Configuration
  * Even more refactoring : search providers
  * continue refactoring
  * back to 0.1.*
  * try again
  * use latest commit
  * rename Indexer to SearchConfiguration
  * Events, CompilerPass, IndexerChain
  * let's try this
  * try * as sherlock dependency
  * highlighting
  * index content from pageparts, index controller iface
  * cleanup
  * index, populate and search nodes
  * more facet testing
  * Merge branch 'master' into 2.2
  * Merge pull request #175 from Kunstmaan/jquery_form
  * Merge pull request #176 from Kunstmaan/menu_offline_state
  * facets
  * Bool query
  * Merge branch 'master' into 2.2
  * Merge pull request #48 from Kunstmaan/menu_offline_state
  * Merge pull request #53 from Kunstmaan/fix/node_versions
  * quickfix to fetch correct node version
  * remove old doc and dependencies
  * sherlock testing
  * remove old code and add Sherlock test code
  * Merge branch 'master' into 2.2
  * Merge pull request #52 from Kunstmaan/fix/slugrouter-fallthrough
  * sherlock 0.1.*
  * "sherlock/sherlock": "~0.1"
  * slug router fallthrough
  * dependencies
  * dependencies
  * dependencies
  * dependencies
  * add Listeners
  * this listener looks unnecessary, it breaks
  * Update TaggingBundle.md
  * fix listener, use NodeEvent, remove MediaEvent for now
  * documentation
  * update naming
  * several fixes, now appears in admin menu
  * new updates and fixes
  * various fixes and updates
  * needed for password reset
  * composer update
  * Merge pull request #51 from Kunstmaan/fix_nodeversionsort
  * fix ordering nodeversions
  * Merge pull request #7 from eymengunay/master
  * fix getnodetranslationforurl
  * fix getnodetranslationforurl
  * Updating bundle to work with Symfony 2.2
  * Merge branch 'fix_findbestmatch' into 2.2
  * Merge pull request #50 from Kunstmaan/fix_findbestmatch
  * fix_findbestmatch: first find online page for the current url
  * Merge pull request #49 from Kunstmaan/fix_slugifier
  * Merge branch 'fix_slugifier' into 2.2
  * fix slugifier: iconv doesn't work as aspected when locale is C or POSIX, see http://www.php.net/manual/en/function.iconv.php
  * added offline state in menu items
  * added offline state in menu items
  * fix extra_actions_header layout
  * Initial commit, Work in Progress
  * Merge branch 'master' into 2.2
  * Merge pull request #95 from Kunstmaan/fix/getmediafromurl
  * follow redirects
  * remove assert annotations
  * remove assert annotations
  * Merge pull request #63 from Kunstmaan/exportwithfiltersandsortparams
  * exportwithfiltersandsortparams
  * exportwithfiltersandsortparams
  * Merge branch '2.2' of git://github.com/Kunstmaan/KunstmaanAdminListBundle into 2.2
  * export all entries that matches the current filtering, not only the 10 visible on the adminlist page
  * Merge pull request #62 from Kunstmaan/fix_adminlistexportall
  * export all entries that matches the current filtering, not only the 10 visible on the adminlist page
  * Merge branch 'master' into feature/symf22
  * Merge branch 'master' into feature/symf22
  * Merge branch 'master' into feature/symf22
  * Merge remote-tracking branch 'origin/master' into feature/symf22
  * Merge remote-tracking branch 'origin/master' into feature/symf22
  * Merge pull request #94 from Kunstmaan/fix/no_handler
  * return FileHandler as default when no handler has been found
  * $event
  * typo fix
  * add Request import
  * add Request as parameter
  * import Request
  * fix php 5.3
  * Merge pull request #8 from Kunstmaan/feature/split_seo
  * fix php 5.3
  * Merge branch 'master' into 2.2
  * Ensure php 5.3 compatibility
  * fix add subform validation
  * fixes_media
  * added jquery_form js
  * added jquery_form js
  * Merge remote-tracking branch 'origin/feature/split_seo' into 2.2
  * Merge pull request #47 from Kunstmaan/previewofflinepages
  * Merge pull request #46 from Kunstmaan/previewofflinepages
  * make it possible to preview offline pages
  * tagging
  * Merge branch '2.2' of git://github.com/Kunstmaan/KunstmaanPagePartBundle into 2.2
  * tagging
  * tagging
  * tagging
  * tagging
  * remove request scope from nodelistener service
  * add Request to constructor
  * add Request parameter
  * Merge branch 'master' into 2.2
  * bugfix getMediaForUrl
  * "monolog/monolog": "1.4.*@dev"
  * remove 'handler'
  * dependencies
  * "sensio/framework-extra-bundle": "2.2.*"
  * SensioGenerator 2.2 dependency
  * symfony 2.2 dependency
  * "friendsofsymfony/user-bundle": "dev-master"
  * Merge branch 'master' into 2.2
  * Merge pull request #92 from Kunstmaan/fix/filehelper
  * fix filehelper issue
  * Corrected namespace
  * Corrected namespace
  * Merge pull request #17 from Kunstmaan/feature/admin-tests
  * test user creation, edit and disable
  * I have a good feeling about this one
  * how about this?
  * let's try this then
  * latest fos/user-bundle
  * no dev-master dependencies
  * no dev-master dependencies
  * no dev-master dependencies
  * change dev-master dependency to * for pagepart
  * change dev-master dependency to * for adminbundle
  * Start of going to Symfony 2.2
  * Start of going to Symfony 2.2
  * Start of going to Symfony 2.2
  * Start of going to Symfony 2.2
  * Fix symfony requirement
  * Merge pull request #1 from Kunstmaan/behat-stable
  * use stable behat bundle versions
  * Fix symfony requirement
  * Fix symfony requirement
  * Fix symfony requirement
  * Fix symfony requirement
  * Fix symfony requirement
  * Start of going to Symfony 2.2
  * Start of going to Symfony 2.2
  * Revert "Start of Start of going to Symfony 2.2"
  * Start of Start of going to Symfony 2.2
  * Start of going to Symfony 2.2
  * Start of Symfony 2.2
  * go to symfony 2.2
  * improved login test, user creation test
  * Add og:url, linkedin url & productID to social
  * Merge branch 'master' into 2.2
  * Merge pull request #67 from Kunstmaan/fix/pagelimit
  * Bugfix for pagelimit check
  * Merge branch 'master' into 2.2
  * Rename Page's title to name in the Form entity
  * Add SEO & set_online functionaliy to PageCreatorService
  * User's groups field is not required
  * settings forms field labels
  * use cleanup
  * refactor forms and add validation
  * Add meta_title + add get_title_for twig extension
  * don't require extrametadata
  * Merge branch 'refs/heads/master' into 2.2
  * don't include deleted nodetranslations by default
  * Merge branch 'master' into 2.2
  * Merge pull request #16 from Kunstmaan/feature/admin-tests
  * generate tests to test the backend after generating the default site
  * Merge branch 'master' into 2.2
  * entity name fix in comment
  * Use 2 FormTypes for 1 Entity
  * Merge branch 'refs/heads/master' into 2.2
  * Tweaks to fix URL uniqueness
  * Merge branch 'master' into 2.2
  * Change dependencies to 2.2.*
  * Merge branch 'refs/heads/master' into 2.2
  * Merge pull request #45 from Kunstmaan/feature/structure_node
  * Make ActionsMenuBuilder less dependent on HasNodeInterface
  * Merge pull request #15 from Kunstmaan/feature/unit-tests
  * move unit test to default site generation
  * and now to dev-master
  * In master always use 'dev-master' for Kuma bundles
  * Fix unittests for ActionsMenuBuilder
  * go to 2.2
  * Use * dependencies in master branch
  * Don't need to set the slug to empty anymore for structurenodes.
  * generate the url for structurenodes but keep the slug empty
  * this is what happens when you don't have unittests :p
  * Clean up use statements
  * Flash message when the URL is automatically modified
  * Simplified NodeTranslationListener
  * Merge branch 'master' into 2.2
  * Modify NodeTranslationListener to don't allow double URL's
  * Fix TestEntity
  * Update Twig/NodeTwigExtension.php
  * node twig extension
  * Merge branch 'master' into 2.2
  * Merge pull request #14 from Kunstmaan/feature/behat
  * Update KunstmaanBehatBundle.php
  * Merge pull request #66 from Kunstmaan/fix/twigextension
  * various fixes
  * Homepage behat test in default site generation
  * fix namespace
  * Generate a FeatureContext which extends the FeatureContext of the KunstmaanBehatBundle
  * fix the pagepart twig extension
  * first commit
  * Oops. Typo in classname.
  * Add PageCreatorService
  * Add ACLPermissionCreatorService
  * Merge branch 'master' into 2.2
  * Merge pull request #65 from Kunstmaan/fix/pagepartrefrepository
  * Merge branch 'master' into 2.2
  * changed abstractpage to haspagepartinterface
  * Add StructureNode Class
  * Merge pull request #91 from Kunstmaan/fix/liip_caching
  * Quickfix for #89 - might need some extra tweaking though, but this should do for the time being.
  * Show save action for StructureNodes
  * remove deprecated call to set for FormView
  * Add StructureNode mode
  * Merge pull request #64 from Kunstmaan/fix/pagepartadmin
  * Merge pull request #13 from Kunstmaan/page-folders
  * use standard invalidargumentexception
  * added use statement
  * changed exception
  * changed abstractpage to haspagepartinterface
  * move page classes to Pages folders continuation
  * Move page classes to Pages folder
  * Merge pull request #41 from Kunstmaan/fix/issue40
  * Merge pull request #12 from Kunstmaan/feature/entity-generator
  * command description update
  * Merge pull request #5 from Kunstmaan/documentation
  * Put everything in one doc like the other bundles
  * Update Resources/doc/NodeBundle.md
  * generate AdminList option in generate Entity command
  * Merge pull request #170 from Kunstmaan/fix/required-message
  * Merge pull request #172 from Kunstmaan/fix/reset-pwd
  * Merge pull request #42 from Kunstmaan/documentation
  * Merge pull request #11 from Kunstmaan/feature/entity-generator
  * Merge pull request #43 from Kunstmaan/fix/view-action-for-adminlists
  * Merge pull request #45 from Kunstmaan/fix/formlist_languages
  * Merge pull request #44 from Kunstmaan/fix/formsubmissions_viewdesign
  * Kunstmaan Entity Generator
  * Updated design of viewing of formsubmissions
  * Only show the formsubmissions for the selected formsubmission page
  * Add translations
  * Add translations
  * Added some information about nodes
  * Merge pull request #37 from Kunstmaan/feature/node_twig_extension
  * Update Twig/NodeTwigExtension.php
  * Update Changelog.md
  * Update README.md
  * removing this line resolves an error when clicking on the confirmation link in the reset password e-mail
  * Merge pull request #61 from Kunstmaan/fix/custom-action-icons
  * show eye-open icon on form view actions
  * use glyph icons for listactions & itemactions
  * Don't offer to copy the current nodetranslation if impossible
  * Modify form controllers to show View action
  * Typo
  * Merge pull request #7 from Kunstmaan/fix_mediaurl
  * Generate error templates
  * fix medialink
  * fix medialink
  * fix mediaurl opengraph image
  * fix mediaurl opengraph image
  * Fixed some typos and duplicate text
  * Merge pull request #60 from Kunstmaan/documentation
  * all in one document + Controller doc update
  * merge with master
  * Merge branch 'master' into 2.2
  * Update README.md
  * more doc tweaks
  * Typo fixes, text tweaks and Filters doc
  * Create an AdminList Configuration
  * minor docs for Controller
  * Added some generator information
  * Merge branch 'master' into 2.2
  * default action no longer needed, it even breaks the site
  * naming convention
  * node twig extension
  * Merge branch 'master' into 2.2
  * Merge pull request #88 from Kunstmaan/feature/folder_internal_name
  * fix - naming conventions
  * internal name support for folders
  * code formatting
  * Merge pull request #20 from Kunstmaan/fix/imagepagepart
  * change imagepagepart to use media.url instead of show
  * code formatting
  * Vote repository refactoring
  * standard Up & Down vote
  * Merge pull request #19 from Kunstmaan/fix/downloadpagepart
  * Use URL method for media in downloadpagepart view
  * fix Container use
  * fix scroll issue on the media popup
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanVotingBundle
  * Added default vote values to config.yml
  * Merge pull request #171 from Kunstmaan/fix/timepicker_defaultTime
  * fix timepicker default value
  * use correct name for twig extension
  * Merge pull request #63 from Kunstmaan/fix/fix-toc
  * Fix TOC to work in frontend and show no preview in backend
  * properly fetch the NodeTranslation objects from the searchindex
  * nop. not 2.0.
  * yeah … so … 2.0.*?
  * depend on elastica 2.0
  * add searchpage and pulled out indexing behavior from src bundles.
  * composer fix
  * Merge branch 'master' into 2.2
  * composer fix
  * Merge branch 'master' into 2.2
  * composer fix
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * composer fix
  * composer fix
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * composer fix
  * Merge branch 'master' into 2.2
  * composer fix
  * composer fix
  * Merge pull request #87 from Kunstmaan/fix_urls
  * Merge branch 'master' into 2.2
  * lock versions
  * composer.json updated
  * Merge branch 'master' into 2.2
  * Merge pull request #86 from Kunstmaan/fix_urls_patch
  * Corrected  Image show template to use url instead of show()
  * Merge pull request #41 from Kunstmaan/feature/email-field
  * basic unit tests for email field
  * email field type
  * use dev-master dependencies on master
  * Merge branch 'refs/heads/2.2'
  * fix menu configurator + add searchcontroller to view performed searched
  * Update composer.json
  * Merge pull request #36 from Kunstmaan/fix/slugifier
  * slashes should be allowed in slugs
  * Update Resources/doc/index.md
  * Loosen dependecy
  * Loosen dependecy
  * Merge pull request #35 from Kunstmaan/fix/newversion_bug
  * typo
  * Merge pull request #34 from Kunstmaan/fix/newversion_bug
  * bugfix nodeversion setonline
  * Merge branch 'master' into fix/required-message
  * Only display "Field is required" when there are no errors - to prevent duplicate NonBlank constraint warnings
  * Header levels
  * Header levels
  * Rename doc folder
  * Update docs
  * Rename documentation
  * fix phpunit
  * Update README.md
  * added a dummy test
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Add a screenshot, refer to the getting started guide and remove outdated or incomplete information.
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Merge pull request #33 from Kunstmaan/fix/slugifier
  * fix spaces
  * fix slugifier nodetranslation
  * Fix typo
  * Merge pull request #59 from Kunstmaan/fix/datepicker_format
  * fix format datepicker in filters
  * datepicker format dd/mm/yyyy
  * Merge branch 'master' into 2.2
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanGeneratorBundle
  * fix CSS parsing
  * Merge branch 'master' into 2.2
  * Default site fixtures tweaks
  * fix template
  * parse namespace
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge pull request #7 from Kunstmaan/fix_choicepagepart
  * Merge pull request #62 from Kunstmaan/fix_pagepart_render_context
  * Merge pull request #39 from Kunstmaan/fixes_formsubmissionslist
  * Merge pull request #40 from Kunstmaan/fix_choicepagepart
  * Default site fixtures tweaks
  * fix template
  * parse namespace
  * Merge branch 'master' into 2.2
  * fix assests generation
  * Update README.md
  * Update README.md
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * Merge pull request #169 from Kunstmaan/update/style
  * update styles and script
  * fix selectlink box
  * update styles
  * Optimize images and update some scripts
  * removed duplicate classes
  * extra documentation
  * fix expanded choice pagepart
  * fix choice pagepart: symfony2.1 upgrade
  * Fixed generating JS and CSS in layout.html.twig
  * Merge pull request #32 from Kunstmaan/fix/datefilter
  * Merge pull request #6 from Kunstmaan/feature/fixturesupdate
  * fix pagepart render context
  * Improve the default fixtures
  * fix: language is no boolean
  * fix exportlink
  * Default frontend look
  * fix for filtering on created at and updated at
  * Set the homepage online
  * css fix for nested_input_prop_container label
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanVotingBundle
  * LinkedIn share integration
  * merging
  * Update Resources/doc/index.md
  * Facebook Send integration
  * datepicker fix
  * Update README.md
  * Update README.md
  * Update README.md
  * fix ugly button on dashboard
  * Merge pull request #58 from Kunstmaan/fix/dbal-adminlist
  * label fixes
  * fixes for DBAL AdminList
  * Merge pull request #30 from Kunstmaan/fix/tree_search
  * Merge branch 'master' into 2.2
  * Merge pull request #38 from Kunstmaan/fix/backend-menu
  * fix for backend issues
  * Merge branch 'master' into 2.2
  * Merge branch 'master' into 2.2
  * fix searchin tree on select window
  * Merge pull request #29 from Kunstmaan/fix/urlchooser_widget
  * fix urlchooser widget / fix slug router
  * Merge pull request #27 from Kunstmaan/feature/save_threshold
  * Merge pull request #54 from Kunstmaan/fix/relocate_add_button
  * placed the parameter in services.yml
  * Merge pull request #57 from Kunstmaan/fix/filter_date_datepicker
  * merge fix/header_brand_font in master
  * Fixed the categories list
  * fixed the datepicker
  * added an inputmask to the date filter
  * empty tests are not allowed
  * changed comments
  * changed save_threshold to version_timeout and added comments
  * Merge pull request #28 from Kunstmaan/fix/slug_router
  * fixed the filter close button, used the new datepicker plugin
  * fix slug router - preview was broken
  * Merge branch 'feature/save_threshold' of git://github.com/Kunstmaan/KunstmaanNodeBundle into feature/save_threshold
  * save threshold when saving page
  * save threshold when saving page
  * changed the font for the brand name back to open sans
  * merged fix/relocate_add_button into master
  * Merge pull request #18 from Kunstmaan/fix/entities
  * Merge pull request #56 from Kunstmaan/fix/required_label_closer
  * Merge pull request #165 from Kunstmaan/fix/format_option_ckeditor
  * label closer to input in edit
  * changed css to put the button on the same line as the header
  * added class extra_actions_header to the add button
  * fix typo
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanFormBundle
  * fix typo
  * remove the format option from the toolbar in ckeditor
  * Merge pull request #164 from Kunstmaan/bootstrap_update
  * fix test actionmenubuilder
  * Update docs for bundles site
  * Update docs for bundles site
  * Update docs for bundles site
  * Update docs for bundles site
  * Update docs for bundles site
  * Centralise the docs for the bundles site
  * Don't need the index
  * Merge pull request #61 from Kunstmaan/fix/no_message_add_pp
  * Update Resources/views/PagePartAdminTwigExtension/widget.html.twig
  * dont show leave msg when adding pp
  * Merge pull request #163 from Kunstmaan/fix/fitler_at_rename
  * table overflow fix
  * renamed filter at to filter on
  * places the label closer to the input field
  * warning fix
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * added the right classes to the warnings when requesting your password
  * Update composer.json
  * Update composer.json
  * Revert "Update composer.json"
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * tree fix
  * deleted console.log
  * auto open the first level of the tree
  * tree element state on search, login user icon, font-weight brand css fixes
  * change default background logo color
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * fix for empty slugs
  * Merge branch 'master' into chainrouter
  * code format/style
  * Merge branch 'chainrouter'
  * Update composer.json
  * Update README.md
  * Update composer.json
  * Update README.md
  * Update composer.json
  * Update README.md
  * Added description
  * Update description
  * Update description
  * Merge pull request #6 from Kunstmaan/feature/meta
  * labels for metatags
  * Merge pull request #161 from Kunstmaan/bootstrap_update
  * Merge pull request #83 from Kunstmaan/fix/media-entity
  * Merge pull request #160 from Kunstmaan/bootstrap_update
  * Merge pull request #55 from Kunstmaan/bootstrap_update
  * Merge pull request #25 from Kunstmaan/bootstrap_update
  * Merge pull request #84 from Kunstmaan/bootstrap_update
  * entity fixes / image pagepart urlchooser & reordering of form fields
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * fix for metadata
  * Removed the btn-danger class from the delete button
  * Merge pull request #24 from Kunstmaan/fix/inverse-relations
  * Merge pull request #4 from Kunstmaan/fix/seo-entity
  * Update .travis.yml
  * properties should be private/protected
  * properties should be private/protected
  * inverse relations added
  * added the class btn-group to the main_actions menu pull-right
  * updated documentation
  * changedupgraded the choose link popup for bootstrap 2.2.2
  * Merge pull request #2 from Kunstmaan/feature/cimkeyword-optional
  * Merge pull request #60 from Kunstmaan/fix/pagepart-markup
  * Merge pull request #17 from Kunstmaan/fix/pagepart-markup
  * Merge pull request #82 from Kunstmaan/fix/pagepart-markup
  * more documentation
  * fixed border on first button main_actions menu
  * fixed border on focussed searchfield
  * cimkeyword is not required
  * Merge pull request #23 from Kunstmaan/feature/menutab
  * get Types by creating in Controller instead of through get Methods
  * Menu tab with : slug, weight and hidden from menu
  * scripts.js fix
  * added the class btn-group to the main_actions menu
  * added the class btn-group to the main_actions menu
  * added the class btn-group to the main_actions menu
  * added the class btn-group to the main_actions menu
  * login button css fix
  * tree fix, datetimepagepart css fix
  * front-end markup for pageparts
  * front-end markup for pageparts
  * front-end markup for pageparts
  * Merge pull request #22 from Kunstmaan/bootstrap_update
  * added class input-append to urlchooser_widget
  * updated the jquery, moderniser version numbers
  * deleted old jquery files
  * updated to bootstrap 2.2.2, updated js libs
  * class added for datetime widget
  * type fix
  * Update README.md
  * value as parameter
  * Merge pull request #16 from Kunstmaan/fix/downloadpagepart
  * Merge pull request #81 from Kunstmaan/fix/videopagepart
  * fix video pagepart markup
  * fix markup download pagepart
  * Repository, Helper, helper service
  * working implementation for Facebook like
  * Merge pull request #59 from Kunstmaan/fix/linkpagepart
  * fixed url chooser route
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * fix urls to old media
  * Update composer.json
  * Facebook Like draft version
  * fix typo composer.json
  * something changed in the master branch on fos
  * Merge pull request #52 from Kunstmaan/fix/uniform_layout
  * Merge pull request #53 from Kunstmaan/fix/pagination
  * Merge pull request #159 from Kunstmaan/fix/reorder_menu
  * fix for addparams in settings
  * relocate the add button on the same line as header
  * reorder menu modules before settings
  * first commit
  * only show pagination if needed
  * uniform layout for adding en editing modules
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * class btn on edit dashboard
  * editmode
  * temp undo to get composer running again
  * Fix deps
  * Composer hell
  * Cleanup composer
  * Cleanup the dependencies
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Merge pull request #18 from Kunstmaan/fix/duplicate_translations
  * Merge pull request #58 from Kunstmaan/feature/open_close_pagepart
  * Merge pull request #20 from Kunstmaan/feature/open_close_pagepart
  * Merge pull request #21 from Kunstmaan/fix/slug_preview
  * fix for slug preview in admin
  * fixed the open/close variable for new pageparts
  * code cleanup
  * open new pagepart fix
  * extraparams method
  * bugfix thanks message
  * fix to open pagepart when added
  * dblick open but not close
  * fix for variable open per pagepart
  * open-close pagepart with editbutton
  * Merge pull request #80 from Kunstmaan/fix/bulk_upload
  * fix for bulk upload
  * Merge pull request #158 from Kunstmaan/origin/fix/datetimepicker
  * update bootstrap-datepicker, set type=text for time
  * fix to prevent duplicate node translations
  * Merge pull request #1 from Kunstmaan/fix/image_url
  * change request.host to request.schemeandhttphost
  * app.request.host for img url
  * fix form widgets - prefix can be empty
  * url chooser _admin
  * Merge pull request #156 from Kunstmaan/feature_dashboard
  * PagePartTab parent persist
  * fix getFilterBuilder
  * Merge pull request #157 from Kunstmaan/feature/date_input
  * cleanup and class for datetime added
  * if fix
  * datemask with datepicker and time
  * removed the unneeded top item in the three
  * reposition flash messages
  * editable dashboard page
  * Merge pull request #154 from Kunstmaan/feature/field_tooltip
  * Merge pull request #16 from Kunstmaan/feature_online_offline_notification
  * Merge pull request #17 from Kunstmaan/fix_fields_not_required
  * Merge pull request #155 from Kunstmaan/feature/twig_date_pattern
  * extra parameter to override date/time pattern
  * weight, slug, hidden from navigation not required
  * text online offline
  * Merge pull request #15 from Kunstmaan/feature_adminlist_explanation
  * explanation text for adminlist
  * Div around a tag link pagepart
  * Merge pull request #14 from Kunstmaan/feature/field_tooltip
  * show field tooltip
  * show field tooltip
  * datepicker added
  * input mask
  * live update preview url
  * date input format __/__/____
  * feature_slug_prefix
  * Merge pull request #12 from Kunstmaan/fix/adminlist_title_edit_action
  * Merge pull request #49 from Kunstmaan/fix/adminlist_title_edit_action
  * template for title and online field
  * use template to make title clickable
  * Merge pull request #152 from Kunstmaan/feature_requiredfields
  * Merge pull request #79 from Kunstmaan/fix_bulkupload
  * fix typo
  * show when a field is required
  * fix_bulkupload + some cleanup
  * changed layout of link
  * Merge pull request #15 from Kunstmaan/fixes_media
  * Merge pull request #78 from Kunstmaan/fixes_media
  * Merge pull request #151 from Kunstmaan/feature_charcount
  * Merge pull request #9 from Kunstmaan/fix/default_weight
  * Merge pull request #11 from Kunstmaan/fix_adminlist_orderby_updated
  * Merge pull request #10 from Kunstmaan/fix_updated_node_version
  * removed tab
  * removed trailing word
  * tab changed with spaces
  * fixes media
  * title clickable edit action
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanNodeBundle
  * fix some minor translations
  * Merge branch 'master' into fixes_media
  * fix video and slide creation
  * Merge branch 'master' into feature_charcount
  * charcount
  * adminlist orderby updated date
  * fix updated time node version
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanMediaBundle
  * fix image selection + name escaping
  * fix conflict between generators, need to override some templates that are created by another generator
  * Fix to work with directory structure
  * set default weight to 0 when creating node translation
  * Merge pull request #37 from Kunstmaan/fix/requiredfields
  * Merge pull request #149 from Kunstmaan/fix/formfields_refactoring
  * Merge pull request #48 from Kunstmaan/feature/add_item
  * Merge pull request #150 from Kunstmaan/feature/flash-messages
  * Merge pull request #77 from Kunstmaan/feature/flash-messages
  * Merge pull request #8 from Kunstmaan/feature/flash-messages
  * Update Resources/views/Chooser/chooserShowFolder.html.twig
  * default-site template tweaks
  * fix possible child namespaces
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * add form as possible child
  * fix error Method getThumbnailUrl for object Kunstmaan\MediaBundle\Helper\Image\ImageHandler does not exist in ...
  * added flash messages to certain actions
  * add flash messages to certain actions
  * add flash messages to certain actions
  * Interate over all FlashMessages and incorporate Twitter Bootstrap coloring scheme
  * refactoring of fields template
  * remove unused template
  * do not add an asterisk to the label of a required field in its model/controller, this should be done in the view
  * Update Entity/NodeTranslation.php
  * Update Entity/NodeVersion.php
  * Update Twig/SeoTwigExtension.php
  * Update Controller/SlugController.php
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanGeneratorBundle
  * lowercase folders removed
  * change case of skeletons
  * fix permission tab
  * f
  * fixes
  * fix form admin type
  * cleanup
  * cleanup
  * fixes
  * f
  * rename twig extension to render_pageparts
  * cleanup + fixes
  * cleanup + fixes
  * cleanup
  * cleanup
  * default site generator config cleanup + refactorings
  * if only one option then no dropdown
  * button updates
  * cleanup
  * cleanup
  * refactor generator bundle
  * refactor generator bundle
  * button on right side
  * fix the admin list generator
  * fix the adminlist generator
  * remove s from path by convention + fixes
  * dropdown add new
  * doc fix
  * Add phpdocs, remove unneeded files
  * Implemented chainrouter
  * Change swiftmailer requirement to version >=4.2.0
  * Use new namespace for ClassLookup
  * Use RouterInterface and not Router
  * Update Controller/SlugController.php
  * fix doctrine entity SEO
  * fix use constant
  * fixes
  * f
  * Merge pull request #5 from Kunstmaan/feature/controller_action
  * Merge branch 'master' into feature/controller_action
  * fix generation of slug for nodetranslation
  * fix imports
  * add AbstractControllerAction
  * add comment
  * fix imports
  * fix test
  * Merge branch 'master' into feature/controller_action
  * make getFormHelper protected
  * fix formHelper
  * cleanup interfaces
  * f
  * not only pages
  * rename dynamic route page interface to dynamic route interface
  * moved to separate branch
  * cleanup HasNodeInterface
  * Merge pull request #4 from Kunstmaan/fix/page_controller_refactoring
  * Merge branch 'fix/page_controller_refactoring'
  * Merge pull request #148 from Kunstmaan/fix/page_controller_refactoring
  * fixes
  * Use FormHelper
  * create FormHelper
  * move twig extensions + create FormHelper
  * fixes
  * add rever command
  * rename PagesController to NodeAdminController
  * rename PagesController to NodeAdminController
  * fix cloning pageparts
  * preview everything
  * update preview so that you can preview versions
  * fix reverting
  * implement revert action
  * update composer
  * extract SEO to separate bundle
  * Implement default Seo functionality for nodes
  * fixes
  * add compiler pass for processors for the kunstmaan logger
  * fixes
  * fixes
  * add logging
  * add custom kuma logger + add user information when logging + use rotating_log_handler
  * remove dependency on pagepart bundle
  * remove dependency on pagepart bundle
  * fixes
  * remove pagepart references
  * fix pagescontroller
  * fix clonehelper
  * fixes
  * refactorings
  * refactorings
  * remove logging
  * cleanup
  * cleanup
  * refactor deepClone
  * rename to deepCloneAndSave
  * rename to deepCloneAndSave
  * rename to deepCloneAndSave
  * listening to page cloning to copy the pageparts
  * fix cloning entities
  * not needed here, it's already in nodebundle
  * Empty line before code blocks for mdoc generation
  * fix page events
  * also add node version
  * add events
  * add events + update tab interface
  * inject request and update page part tab to match the interface
  * rename to nodelistener
  * rename to nodelistener
  * fix for edit pagepart
  * Remove commands
  * refactoring
  * more refactorings .. split delete and add in separate controller action
  * wip
  * more refactorings
  * wip
  * NodeBundle page controller refactoring changes
  * Try to remove dependecies
  * Remove string from method signature
  * Update use statement for namespace change
  * fix dependencies
  * fix node bundle dependency
  * fix construcot for PermissionAdmin in tests
  * add dependecy on node bundle
  * use classlookup from utilitiesbundle
  * use classLookup from utilitiesbundle
  * remove test
  * fix classlookup
  * fix class lookup test
  * Update Resources/doc/menu.md
  * add documentation on how to customize the menu
  * remove the acl changeset event
  * remove the acl changeset event and run the command directly
  * move classllookup to utilitiesBundle
  * add class lookup utility
  * Update Resources/config/services.yml
  * fix slugifier test
  * fix implementation of shell helper
  * implement services by default
  * move stuff to the right bundles
  * move some classes to the right bundles + remove node dependency
  * cleanup
  * Merge pull request #3 from Kunstmaan/fix/adminlists
  * use addFilter method in buildFilters
  * Merge pull request #146 from Kunstmaan/fix/adminlists
  * cleanup
  * cleanup
  * use addFilter method in buildFilters
  * more cleanup
  * fix html for settings forms
  * Merge pull request #2 from Kunstmaan/fix/preview_homepage
  * fixes related to adminlist cleanup
  * adminlist cleanup
  * fixes related to adminlist cleanup
  * fixes related to adminlist cleanup
  * fix form page admin list  test
  * Update README.md
  * added comments
  * fix admin list configurations
  * add Prefix option for tablenames
  * Merge branch 'feature/adminlist_refactor'
  * update admin lists for form submissions
  * update formbundle AdminList
  * cleanup
  * Merge pull request #5 from Kunstmaan/comments
  * return AbstractAdminListConfigurator in stead of the interface so we can chain better
  * Added not-implemented tests
  * fix dependency
  * added comments and typehints
  * cleanup
  * cleanup
  * cleanup
  * cleanup
  * Merge pull request #1 from Kunstmaan/feature/adminlist_refactor
  * Merge pull request #47 from Kunstmaan/feature/adminlist_refactor
  * test simpleitemaction routeGenerator without callable
  * fix the SimpleAction tests
  * fix the SimpleItemAction tests
  * cleanup
  * more refactorings
  * Rename to lowercase
  * Merge branch 'cleanup/deleteactions' into feature/adminlist_refactor
  * refactor action and list action
  * refactor action and list action
  * start refactoring configurator interface
  * start refactoring the configurator interface
  * cleanup
  * f
  * start cleanup AdminBundle
  * fix for fluent interface on addFilter
  * code cleanup
  * cleanup
  * fixed FilterBuilder getter
  * missing use cases
  * bugfix addFilter method
  * refactored / extra parameters added
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * refactor cipher + add shell helper
  * basic tests
  * update documentation
  * removed old elastica methods
  * getter for security context
  * don't need to extends the twigbundle
  * implemented preview, fixed slides import from url, fixed thumbnail for slide
  * fix test media
  * fix wrong test
  * code cleanup
  * code cleanup
  * code cleanup
  * code cleanup, things moved to mediabundle
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * put ckeditor code in adminbundle
  * code cleanup
  * cleanup
  * tests
  * fix phpunit tests
  * fix tests
  * cleanup + name refactorings
  * cleanup + name refactorings
  * cleanup
  * cleanup
  * cleanup
  * media cleanup
  * media cleanup
  * Fix to allow previewing of homepages (pages with empty slug)
  * remove getElasticaView, not the right place to put this
  * adminlist refactoring / typo fix
  * media rework
  * admin list conventions applied to settings admin lists
  * default adminlist conventions implemented in abstract admin list configurators
  * remove getElasticaView from PagePartInterface
  * can not import $this in to a closure
  * remove deprecation, refs #35
  * cleanup
  * cleanup
  * basic unit tests for AdminListFilter
  * unit test for filter
  * unit test skeletons / adminlist fix
  * fix DBAL filters / unit tests for filters
  * unit tests / removed dead code
  * unit tests updated
  * Merge branch 'master' into feature/adminlist_refactor
  * permissions doc updated
  * Merge branch 'master' into feature/fix_guest_user_listener
  * fix guest user listener
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * use camelCase
  * DBAL configurator & filters
  * ORM adminlist permissions
  * adminlist permissions
  * fix twig extension / refactoring
  * css style for admin lists
  * naming conventions applied to abstract classes
  * Merge pull request #4 from Kunstmaan/feature/default-site
  * typo fix
  * php cs fixer
  * php cs fixer
  * refactoring adminlists - ORM in working order...
  * refactoring adminlists - ORM in working order...
  * work in progress
  * work in progress
  * fix tests
  * Tests
  * fix route params
  * setDefaultOptions for PagePartAdminTypes
  * refactoring
  * cleanup and 2.1 fixes
  * avoid conflict with error pages
  * form fixes
  * slug fixes, generate footer js
  * various fixes
  * fix compatibility service method
  * typo
  * wrong file
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanSearchBundle
  * AdminNode -> Node
  * AdminNode -> Node
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanNodeBundle
  * AdminNode -> Node
  * AdminNode -> Node
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * code cleanup
  * AdminNode -> Node
  * find replace CSS and JS include, configure assetic when generating default site
  * Update Resources/doc/form_page_parts.md
  * fixes
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanFormBundle
  * gregwar/form-bundle issue fixed on packagist
  * further refactoring
  * refactorings + documentation
  * service should use RenderContext in stead of array
  * AdminNode -> Node
  * import cleanup
  * AdminNodeBundle -> NodeBundle
  * fix dependency
  * use KunstmaanNodeBundle, no longer KunstmaanViewBundle
  * fix routes
  * Merge branch 'master' into feature/default-site
  * override gregwar repo - bug in packagist?
  * fix background color backend title
  * no dep on viewbundle
  * renamed AdminNode
  * improved twig template generating, composer dependencies
  * fix ck editor
  * fix urlchooser was moved
  * fix code_cleanup
  * ViewBundle merged into NodeBundle
  * merged viewbundle into nodebundle
  * fixes after merge viewbundle
  * Merge remote-tracking branch 'viewbundle/master'
  * rename AdminNodeBundle to NodeBundle
  * rename AdminNodeBundle to NodeBundle
  * code cleanup
  * rename AdminNodeBundle to NodeBundle
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanViewBundle
  * rename AdminNodeBundle to NodeBundle
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanFormBundle
  * rename AdminNodeBundle to NodeBundle
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * rename AdminNodeBundle to NodeBundle
  * code_cleanup
  * unit test for MenuItem
  * ObjectIdentityRetrievalStrategy not needed for SF 2.1
  * unit test for AbstractEntity
  * force Travis to load correct Tests...
  * add basic tests
  * fix for wrong configuration param
  * removed search bundle dependencies / removed pagepart view / removed dead code
  * page part twig extension template moved from view bundle
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminListBundle
  * code cleanup
  * test modified to reflect code changes
  * function comments added
  * sort import statements
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminListBundle
  * more comments
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanFormBundle
  * add more comments
  * add some more comments
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * refactoring adminlists / comments added
  * code cleanup
  * bugfix canEdit
  * code cleanup
  * fix adminlist typehinting
  * fix adminlist typehinting
  * fix adminlist typehinting
  * fix adminlist typehinting
  * fix adminlist typehinting
  * fix adminlist typehinting
  * fix adminlist typehinting
  * fix adminlist typehinting
  * fix adminlist typehinting
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * fix adminlist typehinting
  * removed type hinting in adminlist because it can also be other things then abstractentity
  * Update composer.json
  * removed type hinting in adminlist because it can also be other things then abstractentity
  * removed type hinting in adminlist because it can also be other things then abstractentity
  * function descriptions added / params set to mixed for BC
  * method cannot be private because we need to test it
  * Merge branch 'master' of git://github.com/Kunstmaan/KunstmaanAdminBundle
  * adminlists updated to reflect changes in AdminListBundle
  * Update composer.json
  * Update composer.json
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanPagePartBundle into code_cleanup
  * code cleanup
  * code cleanup
  * Update composer.json
  * code cleanup
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanSearchBundle into code_cleanup
  * code_cleanup
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanMediaPagePartBundle into code_cleanup
  * code cleanup
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanMediaBundle into code_cleanup
  * code_cleanup
  * fix for merge to master
  * Merge branch 'code_cleanup'
  * code cleanup
  * fix for merge to master
  * Merge branch 'code_cleanup'
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanFormBundle into code_cleanup
  * code cleanup
  * fix for merge to master
  * Merge branch 'code_cleanup'
  * fix for merge to master
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanAdminNodeBundle into code_cleanup
  * code_cleanup
  * fix for merge to master
  * Merge branch 'code_cleanup'
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanAdminListBundle into code_cleanup
  * code_cleanup
  * fix for merge to master
  * Merge branch 'code_cleanup'
  * fix for merge to master
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanAdminBundle into code_cleanup
  * code_cleanup
  * fix for merge to master
  * fix for merge to master
  * template generation fix
  * init permissions update
  * fix admins-group & import tweak
  * fix inheritance
  * typo fix
  * fix sensio composer
  * f
  * f
  * fix tabs
  * new templates
  * template generation updates
  * add sensio imports for route template and method + update composer json
  * small fixes
  * wip
  * tweak dependency
  * Update composer.json
  * composer updated
  * more cleaning up
  * composer updated
  * composer updated
  * composer updated
  * composer updated
  * composer updated
  * composer updated
  * composer updated
  * composer updated
  * composer updated
  * composer updated
  * composer updated
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * wip
  * use namespace as option name instead of bundle
  * updated readme
  * NodeRepository refactored to use setRef
  * fix custom backend color
  * fixes & tweaks
  * remove searchpage, fixes
  * refactoring / unit tests / cs fixer
  * code cleanup
  * Merge branch 'code_cleanup' of git://github.com/Kunstmaan/KunstmaanMediaBundle into code_cleanup
  * code cleanup
  * no group fixtures, generator cleanup
  * node unit test modified for setRef
  * fluent interface support for Node setters
  * use setRef instead of 2 separate calls to set reference entity
  * removed dead code / unit test for NodeTranslation
  * unit test for event
  * only one flush in data fixtures
  * refactor generator, generate assets & templates now works
  * refactoring events
  * refactoring events
  * refactoring events
  * refactoring Events
  * naming conventions
  * asset & twig template generation (broken)
  * fix getBestMatchForUrl table names
  * fix twig extension
  * bugfixes services / correct namespaces
  * removed entity_id references / replaced by Gregwar/FormBundle dependency
  * fix field naming conventions
  * twig extension type hint
  * Generate default site fixtures & PagePart admin configurators
  * service naming conventions
  * service naming conventions
  * removed copy of Gregwar FormBundle files / dependency to Gregwar FormBundle added
  * service naming conventions / doctrine entity manager
  * service naming conventions
  * applied naming conventions for services
  * bugfixes / docs updated
  * bugfixes AclHelper / DI
  * fix menubuilder
  * fix menubuilder
  * fix menubuilder
  * SecurityController fix / default twig fix
  * AdminList bugfix / twig fixes
  * bugfix refactoring
  * bugfixes
  * twig template changes - wip
  * twig template changes - wip
  * refactoring / apply acl changeset handled using event & listener
  * Default site generator : entity & form
  * removed remaining AdminNodeBundle references
  * refactoring to remove dependency on AdminNodeBundle...
  * naming conventions / unit test for Node
  * unit tests for constructors added
  * MenuItem fix
  * fix MenuItem
  * MenuItem fix
  * MenuItem fix
  * comments / unit test
  * refactoring / code cleanup
  * fix AdminLists
  * fix PermissionAdmin use statements
  * code cleanup / fluent interfaces
  * code cleanup
  * code cleanup
  * code cleanup
  * naming conventions / php-cs-fixer
  * Merge branch '1.3' into code_cleanup
  * Merge branch '1.3' into code_cleanup
  * Merge branch '1.3' into code_cleanup
  * admin list filters refactored / php-cs-fixer
  * admin list filters refactored
  * admin list filters refactored
  * admin list refactored
  * refactor admin list filters
  * fix parameter for KunstmaanAdminBundle_settings_roles_edit
  * fix parameter for KunstmaanAdminBundle_settings_groups_edit
  * fix parameter for KunstmaanAdminBundle_settings_users_edit
  * Merge branch '1.3' into code_cleanup
  * naming conventions
  * fix SearchPage / php-cs-fixer
  * entity naming conventions
  * php cs fixer
  * fix formtype
  * code cleanup
  * code cleanup
  * added some tests for PermissionAdmin
  * unit test for ClassLookup
  * fixed scope & recursive service call issues
  * code cleanup - wip
  * PHP CS fixes fixes + started fixing  FormSubmissionFields
  * fix int / bool
  * service rename
  * fix int / bool
  * php-cs-fixer
  * service naming fix
  * fixes
  * Merge branch '1.3' into code_cleanup
  * php-cs-fixer
  * code refactored
  * Update README.md
  * Merge branch '1.3' into code_cleanup
  * Update README.md
  * Update Resources/doc/cipher.md
  * fix tests + add documentation
  * Merge branch 'feature/acl_permissions' into 1.3
  * fix for primary button
  * Merge pull request #96 from Kunstmaan/feature/acl_permissions
  * Merge pull request #144 from Kunstmaan/feature/acl_permissions
  * Merge pull request #34 from Kunstmaan/feature/acl_permissions
  * Merge pull request #45 from Kunstmaan/feature/acl_permissions
  * Update README.md
  * Initial commit with Cipher service
  * Merge pull request #64 from Kunstmaan/feature/acl_permissions
  * Merge pull request #56 from Kunstmaan/feature/acl_permissions
  * documentation updated
  * method added to AclHelper that fetches all valid entity IDs for the current user
  * apply acl on pages adminlist
  * Merge branch 'master' into cleanup/deleteactions
  * cleanup the delete action
  * code refactoring
  * code refactoring
  * docs updated
  * documentation updated
  * upgrade docs updated
  * command for basic initialization of ACL permissions
  * test for AclChangeset constructor added
  * test modified
  * comment implemented
  * permissionmap constants
  * permissionmap constants
  * documentation updated
  * adminlists refactored
  * AdminList refactored
  * acl helper refactored
  * adminlist code refactored
  * acl helper code refactored
  * bugfix PermissionAdmin (no permission change & save)
  * comments implemented
  * type hints added
  * type hints added
  * fixes
  * code cleanup
  * unit tests
  * unit tests
  * unit tests added
  * unit tests added
  * Merge branch '1.3' into feature/acl_permissions
  * missing dependency added
  * PermissionAdmin::getRoles unit test added
  * processing comments
  * removed unneeded logging code
  * removed duplicate code / unit test
  * unit tests / code hints added
  * refactored according to standard coding guidelines
  * code cleanup
  * refactoring / code cleanup
  * merge with feature/acl_permissions & refactoring
  * merge with feature/acl_permissions
  * code cleanup
  * code cleanup
  * code cleanup
  * code cleanup
  * code cleanup
  * code cleanup
  * Merge branch 'master' into code_cleanup
  * missing dependency added
  * code cleanup / unit tests
  * quickfix for custom actions
  * Merge branch '2.0'
  * code cleanup
  * code cleanup
  * Merge pull request #143 from Kunstmaan/2.0
  * unit tests
  * Merge pull request #3 from Kunstmaan/develop
  * merge with 1.3
  * unit test modified to reflect changes in actions menu builder
  * merge with 1.3
  * Ignore orig files
  * Merge remote-tracking branch 'origin/2.0'
  * Fix orig
  * Merge remote-tracking branch 'origin/2.0'
  * Fix orig
  * Merge remote-tracking branch 'origin/2.0'
  * Merge pull request #32 from Kunstmaan/feature/formsubmissions
  * Merge pull request #33 from Kunstmaan/feature/formsubmissions
  * Merge branch 'feature/add_extra_sub_actions'
  * Fix orig files
  * Merge remote-tracking branch 'origin/2.0'
  * Change to dev-master
  * Merge remote-tracking branch 'origin/2.0'
  * Merge branch 'feature/add_extra_sub_actions' into 1.3
  * fix
  * documentation for AclHelper
  * changelog
  * changelog
  * changelog
  * changelog
  * changelog
  * documentation
  * code formatting fixed
  * code formatting fixed
  * code formatting fixed
  * code formatting fixed
  * code formatting fixed
  * code formatting fixed
  * Merge branch '1.3' into feature/acl_permissions
  * Merge branch '1.3' into feature/acl_permissions
  * unit test for FormPageAdminListConfigurator
  * better generated code format
  * Merge branch '1.3' into feature/acl_permissions
  * code formatting
  * composer
  * code refactored to follow naming conventions
  * code refactored to follow naming conventions
  * Merge branch '1.3' into feature/acl_permissions
  * unit test for AclChangeset
  * Merge branch '1.3' into feature/acl_permissions
  * unit test skeleton for PermissionAdmin
  * PermissionAdmin code refactored to use object identity retrieval strategy
  * unit tests for Group Entity
  * unit tests
  * extra unit tests
  * unit tests
  * Merge branch '1.3' into feature/acl_permissions
  * composer.json modified for unit tests
  * composer.json modified for unit tests
  * composer.json modified for unit tests
  * composer.json modified for unit tests
  * composer.json modified for unit tests
  * add changelog and upgrade.md file
  * fix typo
  * update changelog
  * add changelog
  * f
  * add upgrade md and feature md file
  * fix use AdminListType namespace
  * rename Listener directory to EventListener
  * rename Listener directory to EventListener
  * add testing + small fixes
  * Add delete link
  * Add delete link
  * translate actions
  * by default translate labels
  * refactor adding extra actions to using the listener
  * refactor action menu's to be configured using knpMenu
  * Remove comma
  * Fix comma
  * merge with 2.0
  * Add webm and mp4 extensions
  * stof/doctrine-extensions-bundle depends on gedmo/doctrine-extensions
  * Add dependency for gedmo translations
  * Use TranslatorInterface and not Translator
  * Merge pull request #55 from Kunstmaan/fix/inspections
  * Merge branch 'master' into fix/inspections
  * Remove duplicate code, fix formatting and bugs and add typecasting, tests docs
  * README fixes
  * Update composer
  * Merge remote-tracking branch 'origin/2.0'
  * Update composer
  * Merge remote-tracking branch 'origin/2.0'
  * Use dev-master in master
  * Merge remote-tracking branch 'origin/2.0'
  * Merge branch 'fix/undefined'
  * Merge branch 'fix/olddocbook'
  * Merge pull request #42 from Kunstmaan/fix/depreciation
  * Merge pull request #41 from Kunstmaan/fix/javascripterror
  * Cleanup the PagePartAdminController
  * Use dev-master
  * Merge remote-tracking branch 'origin/2.0'
  * Fix wring use and filter typing
  * Fix fields array
  * Fix some undefined warnings
  * PHPDoc comment does not match function or method signature
  * Method getEntityManager is deprecated
  * Unnecessary label javascript at line 9
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update the composer.json to have the same settings as Symfony
  * recursively applying permissions is now optional (default = true)
  * removed code used for debugging
  * apply permissions recursively
  * apply permissions recursively
  * code formatting
  * Update composer.json
  * Merge branch '2.0' of git://github.com/Kunstmaan/KunstmaanAdminListBundle into 2.0
  * fixes for multilevel adminlists
  * change to S2.1
  * change to S2.1
  * Merge branch '2.0' of git://github.com/Kunstmaan/KunstmaanAdminBundle into 2.0
  * change to S2.1
  * online status default false
  * adapt native querybuilder to apply ACL
  * Added native helper / bug in helper fixed
  * Added unpublish & extra ACL checks
  * possibility to add extra sub actions on top of the page
  * Unpublish added
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminNodeBundle
  * Merge branch 'fix/verboseadminlist' into feature/formsubmissions
  * add extra sub action to directly link to the form submissions
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanFormBundle
  * CREATE permission commented out - not in use yet...
  * removed dead code / READ -> VIEW / WRITE -> EDIT
  * removed dead code / READ -> VIEW / WRITE -> EDIT
  * removed dead code / READ -> VIEW / WRITE -> EDIT
  * removed all traces of old permissions...
  * removed all traces of old permissions...
  * removed all traces of old permissions...
  * removed all traces of old permissions...
  * removed all traces of old permissions...
  * applied acl to edit form actions
  * removed dummy provider
  * acl proof of concept
  * acl proof of concept
  * acl proof of concept
  * acl proof of concept
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * fix delete dialog
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * fix - remove all permissions from a role
  * subpages copy parent page ACL
  * fix for object identity retrieval strategy when using Doctrine
  * modified to use ACL
  * modifications for ACL permissions
  * modified to use ACL permissions
  * Added minimum-stability to the composer.json
  * Added minimum-stability to the composer.json
  * Added minimum-stability to the composer.json
  * Add language and url of forms to the adminlist
  * Merge pull request #1 from Kunstmaan/feature/bundle
  * Update composer.json
  * Update composer.json
  * S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * change to S2.1
  * Merge branch '2.0' of git://github.com/Kunstmaan/KunstmaanAdminNodeBundle into 2.0
  * change to S2.1
  * Merge branch '2.0' of git://github.com/Kunstmaan/KunstmaanAdminBundle into 2.0
  * change to S2.1
  * wip
  * update css
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * fix title / slug / weight issue
  * Merge pull request #139 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge pull request #134 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge pull request #72 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * test event listener
  * S2.1
  * Update composer.json
  * fix S2.1
  * fix S2.1
  * fix S2.1
  * fix for S2.1
  * Update Resources/config/services.yml
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * Update composer.json
  * NL translation removed
  * quickfix
  * Merge pull request #90 from Kunstmaan/adminnodecleanup
  * Merge pull request #63 from Kunstmaan/adminnodecleanup
  * Merge pull request #89 from Kunstmaan/adminnodecleanup
  * Merge pull request #62 from Kunstmaan/adminnodecleanup
  * cleanup adminnode
  * cleanup adminnode
  * Merge pull request #40 from Kunstmaan/fix/adminlist_filters
  * Merge pull request #138 from Kunstmaan/fix/adminlist_filters
  * fix for adminlist filters
  * fix for date filters
  * fix for adminlist filters
  * fix for date filtering
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminBundle
  * Merge pull request #88 from Kunstmaan/1.3
  * Merge pull request #136 from Kunstmaan/feature/get_users_by_role
  * Merge pull request #137 from Kunstmaan/feature/get_users_by_role
  * added getUsersByRole
  * Merge pull request #30 from Kunstmaan/feature/csv_export
  * Merge pull request #28 from Kunstmaan/feature/csv_export
  * translation fix
  * translation fix
  * cleanup / translation support
  * now using DdeboerDataImport / fix for encoding issue with Excel
  * wip
  * composer dependency added
  * version 1 - handcoded
  * Kunstmaan Bundle generator
  * .settings in gitignore
  * removed eclipse .settings folder
  * fix composer, still needs correct dependencies
  * gitignore for eclipse project files
  * remove unwanted files
  * ReadMe update
  * additional files and gitignore
  * Generator Utils helper
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanGeneratorBundle
  * initial commit
  * Initial commit
  * param removed
  * revert links commented out - functionality not available yet
  * fix for previews (drafts & offline versions)
  * fix for preview (offline pages & drafts)
  * fix for draft preview
  * Merge pull request #135 from Kunstmaan/fix/permissionsfornewgroups
  * Merge pull request #86 from Kunstmaan/fix/drafts
  * fix for draft versions
  * Merge pull request #39 from Kunstmaan/feature/native_queries
  * changes to support native queries (using DBAL QueryBuilder)
  * Revert "try to fix the travis tester"
  * Revert "try to fix the travis tester"
  * try to fix the travis tester
  * try to fix the travis tester
  * make it possible to manage permissions for new groups, not only the ones that where available at top page creation time
  * work in progress
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * fix media pop_up
  * fix image-chooser
  * fix validation
  * remove bancontact stuff
  * Merge pull request #85 from Kunstmaan/fix/nodebyinternalname
  * fix/nodebyinternalname
  * Merge pull request #27 from Kunstmaan/fix/singlelinetext_when_empty
  * move data_class from the value to the admin type
  * Merge pull request #36 from Kunstmaan/fix/adminlist_controller_indexurl
  * Merge pull request #26 from Kunstmaan/fix/from_email_should_not_be_required
  * Merge pull request #84 from Kunstmaan/hotfix/updateslugwhenempty
  * hotfix/updateslugwhenempty
  * From email should not be required
  * Merge pull request #14 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * clearfix on media input field
  * Merge pull request #133 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * fix dropdown menu
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * fix chzn container
  * Merge pull request #132 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * fix chozen select
  * Merge pull request #38 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge pull request #131 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * fix tables
  * fix tables
  * Merge pull request #71 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge pull request #37 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge pull request #130 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * temp fix media chooser
  * temp fix media chooser
  * fix slider
  * Merge pull request #70 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * fix export
  * fix export
  * fix indexurl in the adminlist controller
  * fix sure modal
  * fix
  * Merge pull request #129 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge pull request #35 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge pull request #83 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge pull request #53 from Kunstmaan/upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * fix merge conflict
  * fix merge conflict
  * Merge pull request #34 from Kunstmaan/feature/addDefaultExport
  * fix issue
  * fix issue
  * fix comment with icon
  * fix text with icons
  * afwerkingen
  * afwerking
  * afwerking
  * afwerkinge
  * fix fields
  * fix media
  * fix media image
  * Merge pull request #82 from Kunstmaan/feature/cim_keyword
  * CIM keyword added to SEO (for Club Brugge)
  * fix seo
  * fix media
  * fix settings + media
  * begin fix mediabundle
  * additions media
  * Merge pull request #60 from Kunstmaan/fix_exceptionpage
  * begin upgrade
  * fix
  * begin media
  * fix header pagepart
  * fix filters
  * fix filter
  * prevent breaking existing projects : canExport() by default false, getExportUrlFor() is no longer abstract
  * Merge pull request #33 from Kunstmaan/fix/returnToOverview
  * fixees
  * adjustments
  * added abstract function getExportUrlFor() implementation
  * added canExport() and getExportUrlFor() with export icon in widget.html.twig for list export
  * when throwing an exception the date is not correctly set -> the getAge method throws an exception -> no exception page
  * fix default delete template
  * Fix: don't render modal twig when not used
  * Return back to current list overview when canceling add/edit
  * fix after merge
  * fix after merge
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge pull request #52 from Kunstmaan/feature/admin/validation
  * Merge pull request #81 from Kunstmaan/feature/admin/validation
  * Merge pull request #128 from Kunstmaan/feature/admin/validation
  * feature/admin/validation
  * feature/admin/validation
  * feature/admin/validation
  * annotation fix
  * Merge pull request #80 from Kunstmaan/fix/createemptypage
  * Merge pull request #25 from Kunstmaan/fix/copypage
  * Remove setPermissions() on page entity, this is not implemented
  * Remove invalid deepClone implementation, use parent one
  * Merge pull request #59 from Kunstmaan/fix/dynamicroutingpage
  * fix for dynamic routing pages
  * Merge pull request #79 from Kunstmaan/fix/ckeditor_link_select
  * Fixes to make the link selector from ck work
  * new filter
  * new filter
  * new filters
  * new filters
  * Merge pull request #31 from Kunstmaan/fix/extraparams
  * Provide extraparams to adminlist widget twig
  * Add missing import statement
  * new image
  * new upload img
  * new view
  * Merge pull request #78 from Kunstmaan/fix/implement_slucontroller_changes
  * Fixes for update in slugcontroller
  * Merge pull request #58 from Kunstmaan/fix/implement_slucontroller_changes
  * Merge pull request #51 from Kunstmaan/fix/implement_slucontroller_changes
  * Merge pull request #24 from Kunstmaan/fix/implement_slucontroller_changes
  * Fixes for update in slugcontroller
  * Fixes for updates in slugcontroller
  * updates for slugcontroller
  * Merge pull request #57 from Kunstmaan/fix/slugcontroller_needs_url_not_slug
  * SlugController should ask for url, not for the slug
  * Fix deepClone for pageparts
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * pagepart upgrade
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * fix
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * del unused images
  * fix tree
  * make description fields in SEO textareas
  * Merge pull request #30 from Kunstmaan/fix/delete_confirmation
  * fix delete confirmation (backwards compatibility)
  * Merge pull request #77 from Kunstmaan/feature/add_some_og_properties_by_default
  * Add OG type, title, description and image in the SEO tab, these are needed for allmost every OG implementation
  * Merge pull request #23 from Kunstmaan/fix/formpagevalidation
  * Merge pull request #76 from Kunstmaan/fix/getchildnodes_query
  * fix selecturl action
  * escape refEntityName and fix when parent_id is null
  * fix getChildren for NodeMenuItem to include hidden from navigation pages
  * Thank you text is required for form pages
  * fix
  * Merge pull request #29 from Kunstmaan/fix/adminlistfilters
  * fix/adminlistfilters
  * Merge pull request #75 from Kunstmaan/feature/weightsystem
  * adjustments for club brugge setSequencenumber command
  * Merge pull request #74 from Kunstmaan/feature/children_specify_online_hidden
  * begin fix pagepart
  * update style
  * specify if children should be online or visible in the navigation
  * adjustments
  * fix CKEditor
  * fix CKEditor
  * Merge pull request #125 from Kunstmaan/fix/hasroles_should_also_check_roles_objects
  * fix pageedit
  * fix widget
  * Merge pull request #68 from Kunstmaan/fix_adminlist_getindexurl
  * Merge pull request #73 from Kunstmaan/feature/seoOpengraphInformation
  * Merge branch 'master' into fix_adminlist_getindexurl
  * Merge pull request #69 from Kunstmaan/performance_fixes
  * Merge pull request #72 from Kunstmaan/performance_nodes
  * Merge pull request #127 from Kunstmaan/fix_adminlist_getindexurl
  * Merge pull request #71 from Kunstmaan/fix_adminlist_getindexurl
  * Merge pull request #22 from Kunstmaan/fix_adminlist_getindexurl
  * Merge pull request #15 from Kunstmaan/fix_adminlist_getindexurl
  * Merge pull request #67 from Kunstmaan/soft_delete
  * Move to Liip bundle
  * remove mergeconflict html...
  * searchbar adjustments
  * svg included (logo kunstmaan)
  * don't have to load all children, lazy loading is much better
  * Merge pull request #70 from Kunstmaan/feature/weightsystem
  * Merge branch 'master' into performance_nodes
  * performance nodes part 1: don't load hiddenfromnav items for building the nav
  * fix_adminlist_getindexurl
  * fix_adminlist_getindexurl
  * fix_adminlist_getindexurl
  * fix_adminlist_getindexurl
  * fix_adminlist_getindexurl
  * Merge branch 'feature/weightsystem' of github.com:Kunstmaan/KunstmaanAdminNodeBundle into feature/weightsystem
  * update ending flush after loop
  * Merge branch 'master' into upgrade_bootstrap_v2.0.4
  * fix merge conflicet
  * del old png logo
  * login + reset pwd
  * adding field with extra data to SEO for OpenGraph
  * soft delete items and folders
  * Merge branch 'feature/weightsystem' of github.com:Kunstmaan/KunstmaanAdminNodeBundle into feature/weightsystem
  * update max nesting level
  * Merge pull request #50 from Kunstmaan/feature/weightsystem
  * modify getTopNodes
  * Merge branch 'feature/weightsystem' of github.com:Kunstmaan/KunstmaanAdminNodeBundle into feature/weightsystem
  * Merge branch 'feature/weightsystem' of github.com:Kunstmaan/KunstmaanAdminNodeBundle into feature/weightsystem
  * small refactoring
  * has roles should also check inside the roles objects
  * Merge branch 'feature/weightsystem' of github.com:Kunstmaan/KunstmaanAdminNodeBundle into feature/weightsystem
  * making use of iterate (so no out of memory will be thrown) + only set weight when it is null
  * Merge pull request #21 from Kunstmaan/fix/broketostringsubmfields
  * Merge pull request #66 from Kunstmaan/fix/imagine_routing
  * remove imagina from routing
  * add feedback while executing
  * rename sequence to weight command
  * fix choice field submission toString when submitted value equals 0
  * Command to update all nodetranslations weights, based on the nodes sequencenumber
  * Merge pull request #20 from Kunstmaan/fix/choiceinputvalidation
  * Add empty value to select, fix null validation on choice input
  * Merge pull request #69 from Kunstmaan/feature/weightsystem
  * add second order by property to sort by title
  * Merge pull request #66 from Kunstmaan/feature/pageHeader
  * don't remove the title in admin type
  * Merge pull request #68 from Kunstmaan/feature/weightsystem
  * Merge pull request #19 from Kunstmaan/fix/broketostringsubmfields
  * const call: use full class name
  * Merge pull request #18 from Kunstmaan/fix/broketostringsubmfields
  * Fix broke toString on String- and Text submission fields
  * weightsystem ASC, lightest weight appears on top
  * show pageTitle in adminType
  * Merge pull request #124 from Kunstmaan/feature/weightsystem
  * Merge pull request #67 from Kunstmaan/feature/weightsystem
  * implementation weightsystem
  * update implementation weightsystem
  * Merge pull request #17 from Kunstmaan/feature/redirectthankyoupage
  * On successful form submit, redirect to myself, adding 'thanks' parameter
  * Merge pull request #16 from Kunstmaan/fix/ppvalidation
  * Don't use required attribute
  * Form pp error translations
  * Merge pull request #123 from Kunstmaan/fix_branding_color
  * fix branding color
  * Merge pull request #49 from Kunstmaan/fix/delete_reorder_pageparts
  * bugfix - reordering/deleting pageparts
  * Fix default required error message - remove from getter
  * Merge pull request #27 from Kunstmaan/1.2.1
  * BC break documentation
  * fix bad merge
  * prepare upgrade
  * Merge branch 'master' into 1.2.1
  * implementation weightsystem for menu
  * Merge pull request #25 from Kunstmaan/feature/indexpath
  * Merge branch '1.2' into feature/indexpath
  * Merge pull request #26 from Kunstmaan/feature/numberfilterandcustomcellview
  * Provide form.bound parameter to form widgets
  * added login html
  * Merge pull request #13 from Kunstmaan/feature/fileuploadpp
  * logincss + fontawsome
  * fix js tree
  * listview
  * edit search
  * Implementation of field pagetitle in abstractpage
  * login finish
  * login
  * File upload form submission mail template
  * File upload form submission view template
  * Added file upload field + hook method for page parts on valid form post
  * Merge pull request #48 from Kunstmaan/feature/haspageparts
  * method to check if pageparts with specific context have been attached to an entity
  * update js impl
  * upgrade baseview and index
  * Merge pull request #56 from Kunstmaan/fix/getparams
  * pass get params to the request
  * Merge pull request #12 from Kunstmaan/feature/choicesubmissionfield
  * Form page parts default backend view
  * Form submission value field, choice pp improvements
  * Merge pull request #65 from Kunstmaan/fix/propertiestab_when_no_pageparts
  * Merge pull request #117 from Kunstmaan/fix/dynamicroutes_inherits_abstractpage
  * dynamic routes should extend abstract page
  * Show propertiestab when there are no pageparts
  * Refactor form page parts context: extract to method
  * fix
  * Added ChoiceFormSubmissionField to persist ChoicePagePart submissions
  * Merge pull request #116 from Kunstmaan/update_css_for_jquery_chosen
  * Merge pull request #47 from Kunstmaan/feature/limitpponpage
  * page part backend view
  * Limit the instances of a certain page part type that can be added to a page.
  * update css file
  * Merge pull request #63 from Kunstmaan/feature/event_listeners_for_media_update_and_create
  * add event listeners + fix update metadata
  * Merge pull request #63 from Kunstmaan/fix/getbestmatchforurl
  * bugfix for softdeletes
  * Merge pull request #62 from Kunstmaan/feature/post_edit_event_dispatch
  * add a postEdit event
  * Implementation exportfields
  * Merge pull request #54 from Kunstmaan/fix/dynamicroutingpage
  * removed comments used for testing...
  * Merge branch 'master' into fix/dynamicroutingpage
  * redirect to default language fix
  * Merge pull request #53 from Kunstmaan/fix/dynamicroutingpage
  * routing speedup / 404 handler fix
  * Merge pull request #56 from Kunstmaan/fix/intelligent-slug-generator
  * Merge pull request #113 from Kunstmaan/fix/intelligent-slug-generator
  * intelligent slug behaviour
  * allow empty slug on request
  * Merge pull request #51 from Kunstmaan/fix/dynamicroutingpage
  * Merge pull request #112 from Kunstmaan/fix/dynamicroutingpages
  * bugfix
  * modified dynamic routing page handling
  * Merge branch 'master' into fix/dynamicroutingpage
  * modified dynaming routing pages handling
  * Merge pull request #50 from Kunstmaan/fix_issue_5585
  * improved code
  * fixed issue 5585 slug with slash
  * Merge pull request #59 from Kunstmaan/feature/bulkupload_and_cleanup
  * bulkupload + styling
  * fix styling
  * Merge pull request #58 from Kunstmaan/fix_show_folders_with_images
  * fix show folders with images + show scroll
  * Merge pull request #111 from Kunstmaan/feature/twigtemplate
  * Update for the twig template
  * Merge pull request #47 from Kunstmaan/fix/dynamicroutingpage
  * fix for dynamic routing pages : code cleanup / support for drafts
  * fix for dynamic routing pages : code cleanup / support for drafts
  * Merge pull request #11 from Kunstmaan/fix/annotations
  * Merge pull request #57 from Kunstmaan/fix/annotations
  * Merge pull request #46 from Kunstmaan/fix/annotations
  * code cleanup / fix annotations
  * code formatting / annotations cleanup
  * Merge branch 'master' into fix/annotations
  * code formatting / annotations cleanup
  * Merge pull request #110 from Kunstmaan/fix/annotations
  * code formatting / annotations cleanup
  * Merge pull request #56 from Kunstmaan/fix/annotations
  * Merge branch 'master' into fix/annotations
  * Merge pull request #55 from Kunstmaan/fix/annotations
  * Merge pull request #109 from Kunstmaan/fix/annotations
  * fix annotations
  * fix annotations
  * annotations fixed
  * Merge pull request #108 from Kunstmaan/cleanup
  * Add description to composer.json
  * Merge branch 'master' into cleanup
  * Code style
  * Fix readme
  * Update readme
  * Merge pull request #45 from Kunstmaan/fix/linepagepart
  * comment for fix added in twig template
  * Merge branch 'master' into fix/linepagepart
  * prevent linepagepart text from showing at bottom of form
  * Code style
  * Fix code style
  * code cleanup
  * Typo
  * json fix
  * Update readme's
  * Missing a word in the description
  * Update the readme to reflect the latest changes
  * Some formatting cleanup
  * Remove repositories
  * Remove repositories
  * Removing repositories
  * Travis and Composer configs
  * Remove repositories
  * Travis and Composer configs
  * Remove repositories
  * Travis and Composer configs
  * Remove repositories
  * Remove repositories
  * Fix pageparts twig extension in services.yml, case sensitivity
  * Merge pull request #46 from Kunstmaan/move_pageparts_twig_extension_to_pagepart_bundle
  * Merge pull request #44 from Kunstmaan/get_pageparts_twig_extension
  * Merge pull request #55 from Kunstmaan/media_refactorings
  * Missing methods filled up with stubs
  * Dependencies
  * Travis config
  * deleting seems to work !!
  * Add the adminbundle dependency
  * Adding deps
  * Merge branch 'master' into media_refactorings
  * fix
  * Travis config
  * Dependencies
  * only show metadata button if needed
  * Travis config
  * translations
  * further refactoring of media menu adaptor
  * refactoring media view
  * edit metadata for images etc. works
  * cleanup
  * cleanup
  * editing metadata works
  * Add deps
  * Add deps
  * Update gitignore
  * Travis config
  * possibility to configure metadata and input it when inserting new media
  * Update gitignore
  * Travis configs
  * ignore vendor content
  * Travis configs
  * Fix missing methods with stubs
  * Full repository list
  * Full repository list
  * Full repository list
  * Remove adminbundle for now
  * Move to master
  * Deps still broken, but needed for now
  * Remove idea files
  * Merge pull request #54 from Kunstmaan/tests/setuptraviscomposer
  * Improve gitignore
  * Setup
  * cleanup
  * cleanup
  * Merge pull request #21 from Kunstmaan/tests/setuptraviscomposer
  * cleanup
  * Merge pull request #107 from Kunstmaan/feature/travis-composer-dependencies-versioning
  * Move to the master branches, this will break until everything is done
  * Folder should extend AbstractEntity
  * cleanup
  * cleanup
  * cleanup
  * cleanup
  * Update gitignore
  * Fixed a compilation error, this needs to be checked
  * Update build scripts
  * Merge pull request #53 from Kunstmaan/fix_best_match_for_url_method
  * Improved TravisCI integration and dependency graph in composer.json
  * re-add the best match for url method
  * Cleanup
  * Cleanup
  * Cleanup
  * Update composer
  * Added getIndexUrl for returning to overview after delete/edit/add action
  * Merge pull request #105 from Kunstmaan/feature/jquery_ui_autocomplete
  * jquery ui autocomplete / jquery update
  * Merge pull request #10 from Kunstmaan/fix/adminlist
  * Merge pull request #101 from Kunstmaan/fix/adminlist
  * Merge pull request #52 from Kunstmaan/fix/adminlist
  * Merge pull request #20 from Kunstmaan/feature/customactions
  * Fix for new adminlist functionality
  * Fix for new adminlist functionality
  * Fix for new adminlist functionality
  * Merge pull request #9 from Kunstmaan/feature/form_entityid_type
  * support for hidden entity id field
  * getCustomActions disappeared
  * merge with master
  * fixes for delete
  * remove loading of template in getPageparts
  * Merge branch 'get_pageparts_twig_extension' of github.com:Kunstmaan/KunstmaanPagePartBundle into get_pageparts_twig_extension
  * merge
  * cleanup and add export actions
  * fix add action + remove invalid export link
  * Add pageparts twig extension
  * Add pageparts twig extension
  * Move pageparts twig extension to pageparts bundle
  * Merge pull request #99 from Kunstmaan/fix_move_ck_config_to_separate_template
  * Merge pull request #51 from Kunstmaan/fix/cleanup
  * add ckeditor.js.twig the default config
  * make add subpage a method
  * abstractentities_and_cleanup
  * Merge pull request #13 from Kunstmaan/abstractentities_and_cleanup
  * Merge pull request #8 from Kunstmaan/abstractentities_and_cleanup
  * Merge pull request #45 from Kunstmaan/abstractentities_and_cleanup
  * Merge pull request #43 from Kunstmaan/abstractentities_and_cleanup
  * Merge pull request #96 from Kunstmaan/abstractentities_and_cleanup
  * Merge pull request #13 from Kunstmaan/abstractentities_and_cleanup
  * Merge pull request #50 from Kunstmaan/abstractentities_and_cleanup
  * Merge pull request #98 from Kunstmaan/fix_titlecolor
  * Merge pull request #53 from Kunstmaan/fix_scroll
  * title color fix
  * Merge pull request #97 from Kunstmaan/fix_move_ck_config_to_separate_template
  * color is customizable
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * move ck config to separate template, this way we can override it
  * fix scrolling in choosers
  * Merge branch 'master' into abstractentities_and_cleanup
  * there must be one test
  * Merge branch 'master' into abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * abstractentities_and_cleanup
  * Merge pull request #49 from Kunstmaan/fix_page_adaptor_when_no_id_2
  * fix
  * Merge pull request #19 from Kunstmaan/feature/deletehook
  * refactored to pass item instead of id to delete hook
  * added support for delete hook
  * Merge pull request #12 from Kunstmaan/fix_index_offlinepages
  * Merge pull request #48 from Kunstmaan/fix_index_offlinepages
  * Merge pull request #95 from Kunstmaan/dynamicroutingpage
  * Merge pull request #47 from Kunstmaan/dynamicroutingpage
  * Merge pull request #44 from Kunstmaan/dynamicroutingpage
  * fix_index_offlinepages
  * fix_index_offlinepages
  * support for dynamic routing pages
  * Merge branch 'dynamicroutingpage' of github.com:Kunstmaan/KunstmaanAdminNodeBundle into dynamicroutingpage
  * Merge branch 'dynamicroutingpage' of github.com:Kunstmaan/KunstmaanAdminBundle into dynamicroutingpage
  * Merge pull request #46 from Kunstmaan/fix_page_adaptor_when_no_id
  * fix page adaptor when no id
  * Merge pull request #43 from Kunstmaan/fix_highlightandurls
  * fixes:
  * Merge pull request #52 from Kunstmaan/fix_media_menu_adaptor
  * dynamic url matcher
  * fix nullpointers in MediaMenuAdaptor
  * Merge pull request #93 from Kunstmaan/fix/cleanup
  * Fix when lowestTopChild does not exist
  * dynamic routing hack...
  * fix nullpointer in MediaMenuAdaptor
  * Merge pull request #17 from Kunstmaan/alias_support
  * Merge pull request #18 from Kunstmaan/fix_default_actions_post_on_current_uri
  * Merge pull request #45 from Kunstmaan/fix/cleanup
  * Abstract AdminListController + delete entity as POST action + export to csv
  * Merge pull request #92 from Kunstmaan/fix/cleanup
  * Merge pull request #51 from Kunstmaan/fix/cleanup
  * Merge pull request #42 from Kunstmaan/fix/cleanup
  * Merge branch 'master' into fix/cleanup
  * optimize and decrease number of queries
  * Merge branch 'master' into fix/cleanup
  * optimize and decrease number of queries
  * Merge branch 'master' into fix/cleanup
  * optimize and decrease number of queries
  * Merge branch 'master' into fix/cleanup
  * optimize and decrease number of queries
  * add twig extension to get current router params + make default actions post on current uri
  * alert removed
  * code cleanup
  * support for aliased columns
  * get best match for url
  * dynamic routing page
  * Merge pull request #42 from Kunstmaan/fix_add-locale-to-rendercontext
  * add locales to the render context, and replace tabs with spaces
  * Merge pull request #44 from Kunstmaan/fix_no_auto_update_slug
  * do not automatically update slugs
  * Merge pull request #41 from Kunstmaan/fix/cleanup
  * Merge pull request #41 from Kunstmaan/fix/cleanup
  * Merge pull request #12 from Kunstmaan/fix/cleanup
  * Merge pull request #50 from Kunstmaan/fix/cleanup
  * Removed unneeded comments and general cleanup of comments
  * Removed unneeded comments and general cleanup of comments
  * Removed unneeded comments and general cleanup of comments
  * Removed unneeded comments and general cleanup of comments
  * Merge pull request #7 from Kunstmaan/fix/cleanup
  * Removed unneeded comments and general cleanup of comments
  * Merge pull request #43 from Kunstmaan/fix/cleanup
  * Removed unneeded comments and general cleanup of comments
  * Merge pull request #16 from Kunstmaan/fix/cleanup
  * Merge pull request #91 from Kunstmaan/fix/cleanup
  * Removed unneeded comments and general cleanup of comments
  * Removed unneeded comments and general cleanup of comments
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanMediaPagePartBundle
  * wrong namespace
  * New hook for adding custom actions other than delete / edit
  * Merge pull request #6 from Kunstmaan/modulesupport
  * Merge pull request #90 from Kunstmaan/modulesupport
  * Added check on has* functions in value lookup
  * Merge pull request #14 from Kunstmaan/feature/numberfilterandcustomcellview
  * Merge pull request #42 from Kunstmaan/fix_nodetranslationbyurl
  * Merge pull request #40 from Kunstmaan/fix_nodetranslationbyurl
  * fix add parameter lang
  * add locale to nodetranslation by url methode
  * add locale to getnodetranslationbyurl
  * Moved form submissions to Modules menu
  * support for modules
  * Merge pull request #49 from Kunstmaan/fix_getcontenttype
  * Merge pull request #11 from Kunstmaan/fix_getcontenttype
  * use short contenttype
  * get contenttype withouth the application-type
  * Merge pull request #40 from Kunstmaan/fix_nodetranslationforslug
  * change where and andWhere
  * Merge pull request #39 from Kunstmaan/fix/add-url-to-nodetranslations
  * Merge pull request #39 from Kunstmaan/fix/add-url-to-nodetranslations
  * refresh node when creating a nodetranslation
  * Merge pull request #11 from Kunstmaan/reworkmenu2
  * Merge pull request #48 from Kunstmaan/reworkmenu2
  * Merge pull request #5 from Kunstmaan/reworkmenu2
  * Merge pull request #38 from Kunstmaan/reworkmenu2
  * Merge pull request #88 from Kunstmaan/reworkmenu2
  * reworkmenu2
  * reworkmenu2
  * reworkmenu2
  * reworkmenu2
  * reworkmenu2
  * Merge pull request #47 from Kunstmaan/fix_scroll
  * fix scrolling in choosers
  * updated command
  * Fixes issue when nodes are multple levels deep
  * flush in updateNodeChildren
  * Fixed return
  * Added some comments & made it nicer
  * fix getSlugPart:
  * wrong documentation
  * feature/numberfilterandcustomcellview
  * work with urls now
  * also do some stuff postPersist
  * added url and url update command. This will break stuff.
  * Merge pull request #37 from Kunstmaan/fix_cklinkchooser
  * fix tree when page is offline
  * Merge pull request #36 from Kunstmaan/fix_nodetranslation
  * nodetranslationbyslugpart fix
  * check if slug is null and do another query
  * Merge pull request #35 from Kunstmaan/fix/routing-matches-subnodes
  * If parent is null, parent should be null
  * Merge pull request #34 from Kunstmaan/fix_slug
  * rtrim on slash
  * fix recursivity
  * make getFullSlug recursive
  * Merge pull request #33 from Kunstmaan/editableandemptyslugs
  * Merge pull request #38 from Kunstmaan/editableandemptyslugs
  * editableandemptyslugs
  * editableandemptyslugs
  * Merge pull request #32 from Kunstmaan/fix_routing
  * Merge pull request #87 from Kunstmaan/fix_reset
  * from adminbundle to  adminnodebundle route fix
  * fix reset login
  * Merge pull request #86 from Kunstmaan/fix/adminnoderef
  * Merge pull request #31 from Kunstmaan/fix/adminnoderef
  * fix/adminnoderef
  * fix/adminnoderef
  * Merge pull request #85 from Kunstmaan/feature/menu-rework-cleanup
  * feature/menu-rework-cleanup#
  * Merge pull request #84 from Kunstmaan/feature/menu-rework
  * Merge pull request #13 from Kunstmaan/feature/menu-rework
  * Merge pull request #30 from Kunstmaan/feature/menu-rework
  * Merge pull request #4 from Kunstmaan/feature/menu-rework
  * Merge pull request #46 from Kunstmaan/feature/menu-rework
  * Merge pull request #10 from Kunstmaan/feature/menu-rework
  * feature/menu-rework
  * feature/menu-rework
  * feature/menu-rework
  * feature/menu-rework
  * feature/menu-rework
  * feature/menu-rework
  * Merge pull request #39 from Kunstmaan/fix_ckeditor
  * fix no paragraphs in ck when moving pagepart
  * Merge pull request #83 from Kunstmaan/feature/datetwigextension
  * date by locale twig extension
  * Merge pull request #29 from Kunstmaan/fixlookuppagebyinternalnameandparent
  * fixlookuppagebyinternalnameandparent
  * Merge pull request #82 from Kunstmaan/feature/propertiescontext
  * add propertyfields in other tabs via context
  * Merge pull request #37 from Kunstmaan/fix_locale
  * redirect to slug_draft in draft
  * redirect when wrong locale
  * Merge pull request #36 from Kunstmaan/fix_locale
  * define fallback as first item of requiredlocales array
  * Merge pull request #38 from Kunstmaan/fix_tree
  * Merge pull request #45 from Kunstmaan/fix_tree
  * Merge pull request #81 from Kunstmaan/fix_tree
  * tree fix
  * tree fixes
  * fix tree
  * Merge pull request #35 from Kunstmaan/fix_locale
  * var_dump fix
  * strtok fix
  * fix vardump
  * check required locales
  * Merge pull request #34 from Kunstmaan/fix/locale
  * fix locale from url
  * Merge pull request #37 from Kunstmaan/fix_rawhtmlpagepart
  * fix elasticaview
  * fix raw html pagepart
  * Merge pull request #36 from Kunstmaan/fix_elastica
  * Merge pull request #2 from Kunstmaan/fix_elastica
  * Merge pull request #10 from Kunstmaan/fix_elastica
  * fix isPagePart
  * fix isPagePart
  * fix isPagePart
  * Merge pull request #35 from Kunstmaan/fix_elastica
  * add elasticaview to pageparts
  * Merge branch 'fix_elastica' of github.com:Kunstmaan/KunstmaanPagePartBundle into fix_elastica
  * Merge branch 'fix_elastica' of github.com:Kunstmaan/KunstmaanMediaPagePartBundle into fix_elastica
  * Merge pull request #44 from Kunstmaan/fix_editvideo
  * fix edit video + chooser
  * no title in videoview
  * Merge pull request #33 from Kunstmaan/feature/slug-without-view
  * support for slugs without view (redirects)
  * support for slugs without view (redirects)
  * Merge pull request #80 from Kunstmaan/feature/hide-add-subpage
  * hide add subpage button when there are no possible childpage types
  * Merge pull request #34 from Kunstmaan/feature/rawhtmlpagepart
  * fix doc
  * raw HTML pagepart
  * Merge pull request #43 from Kunstmaan/fix_ckselectimage
  * ckeditor select image fix
  * Merge pull request #79 from Kunstmaan/fix_ckselectlink
  * fix select link in ckeditor
  * Merge pull request #33 from Kunstmaan/feature/addedittemplate
  * Use editTemplate if available
  * Extend copyright notice in the license file
  * typo in README.md
  * Merge pull request #42 from Kunstmaan/fix_license
  * fix licensing
  * Merge pull request #32 from Kunstmaan/fix_redirectinservice
  * redirect in service methode of page
  * fix elastica search for assets
  * fix search on assets
  * Merge pull request #31 from Kunstmaan/fix_hightlights
  * trim highlights
  * Merge pull request #30 from Kunstmaan/feature/rendercontext
  * Merge branch 'master' into feature/rendercontext
  * feature/rendercontext
  * Merge pull request #12 from Kunstmaan/feature/refactorabstractadminlistconfigurator
  * Merge pull request #78 from Kunstmaan/feature/refactorabstractadminlistconfigurator
  * feature/refactorabstractadminlistconfigurator
  * feature/refactorabstractadminlistconfigurator
  * Merge pull request #29 from Kunstmaan/feature/resourceparam
  * Merge pull request #77 from Kunstmaan/fix/entitymanagerclosedexception
  * feature/resourceparam
  * fix/entitymanagerclosedexception
  * Merge pull request #76 from Kunstmaan/feature/noparagrahps
  * feature/noparagraphs
  * Merge pull request #75 from Kunstmaan/fix_preview
  * published page preview fix
  * Merge pull request #28 from Kunstmaan/feature/pageparts
  * get pageparts and get pageparts widget
  * Merge pull request #9 from Kunstmaan/fix_imagepagepart
  * clearfix under image
  * Merge pull request #28 from Kunstmaan/fix/nodebyinternalname
  * fix/nodebyinternalname
  * Merge pull request #74 from Kunstmaan/fix/guests
  * fix/guests
  * Merge pull request #73 from Kunstmaan/feature/ckeditorheight
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminNodeBundle
  * feature/nodemenubyinternalname
  * feature/ckeditorheight
  * Merge pull request #72 from Kunstmaan/fix_changepass
  * update settingscontroller to change password
  * Update README.md
  * Merge pull request #71 from Kunstmaan/fix_favicon
  * favicon fix
  * fix favicons in layout
  * Merge pull request #27 from Kunstmaan/fix_getslug
  * get slug from non-deleted item
  * Merge pull request #7 from Kunstmaan/fix_imagepagepart
  * p-element around image for spacing under and above
  * fix for unpublished nodes
  * Merge pull request #70 from Kunstmaan/fix/autocreateguestpermission
  * fix/autocreateguestpermission
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanSearchBundle
  * fix search
  * Merge pull request #6 from Kunstmaan/fix_imagepagepart
  * no max-width on image
  * Merge pull request #26 from Kunstmaan/fix_hide
  * hide nodes from navigation
  * Merge pull request #69 from Kunstmaan/fix_movenodes
  * fix move nodes
  * Merge pull request #68 from Kunstmaan/fix_movenodes
  * fix movenodes, dynamic url instead of static
  * Merge pull request #67 from Kunstmaan/fix_logging
  * fix loghandler error on getting user when not logged in
  * Merge pull request #27 from Kunstmaan/fix_layoutsearch
  * add containerclass of homepage check to define layout
  * Merge pull request #25 from Kunstmaan/feature/nodeinternalnames
  * feature/nodeinternalnames: possible to give internal names to nodes so you can link to them from templates
  * Merge pull request #9 from Kunstmaan/fix_searchmenuitem
  * Merge pull request #66 from Kunstmaan/fix_searchmenuitem
  * searches not in menu when searchbundle not set
  * searchbundle var in config
  * Merge pull request #24 from Kunstmaan/fix_menu
  * Merge pull request #65 from Kunstmaan/fix_menu
  * menu adaptor for pages menuitem
  * pages not in menubuilder
  * Merge pull request #64 from Kunstmaan/fix_editmod
  * Merge pull request #32 from Kunstmaan/fix_scrolltopagepart
  * scroll to added pagepart
  * fix open tab + editmode
  * added pagepart in editmode
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Update .travis.yml
  * Merge pull request #40 from Kunstmaan/fix_imageresize
  * smaller images are not made bigger to be in the right format
  * Merge pull request #39 from Kunstmaan/fix_mediachoosers
  * only folders with files, title with images
  * Merge pull request #63 from Kunstmaan/fix_font
  * font not in stylesheetstag
  * Merge pull request #62 from Kunstmaan/fix_saveonclose
  * merge with master
  * leave page modal fix on save+delete+...
  * Merge pull request #61 from Kunstmaan/fix/versions
  * Merge pull request #23 from Kunstmaan/fix/versions
  * Merge pull request #26 from Kunstmaan/fix/versions
  * Merge branch 'master' into fix/versions
  * fix/versions
  * fix/versions
  * fix/versions
  * Merge pull request #38 from Kunstmaan/fix_mediainck
  * Merge pull request #60 from Kunstmaan/fix_mediainck
  * if mediabundle is defined set imagebrowser
  * Merge pull request #59 from Kunstmaan/fix_layout
  * Merge pull request #37 from Kunstmaan/fix_layout
  * Merge pull request #31 from Kunstmaan/fix_layout
  * extra var to check if media in ckeditor should be loaded
  * linkbrowser update
  * extend default layout in choosers
  * extending linkbrowser layout
  * Merge pull request #36 from Kunstmaan/fix_titlefont
  * Merge pull request #58 from Kunstmaan/fix_linkselector
  * right brand font in menubar
  * new layout for link selector
  * Merge pull request #57 from Kunstmaan/fix/no-langswitch-when-only-one-lang
  * Dont display the language chooser when there is only one language
  * Update Resources/translations/messages.en.yml
  * Merge pull request #56 from Kunstmaan/fix_propertiesform
  * extra check at seo tab
  * properties in first tab and open first tab on load
  * Merge pull request #55 from Kunstmaan/fix_stayintab
  * stay in tab on update page
  * Merge pull request #30 from Kunstmaan/fix_addpageparts
  * add pagepart when all pageparts deleted
  * Merge pull request #53 from Kunstmaan/fix_ckeditor
  * Merge pull request #54 from Kunstmaan/fix_leavepage
  * Merge pull request #29 from Kunstmaan/fix_savepageparts
  * new line at end of readme
  * fix readme
  * fix readme
  * Merge branch 'master' into fix_ckeditor
  * Merge branch 'master' into fix_leavepage
  * alert when leaving page
  * ask to save page
  * Merge pull request #5 from Kunstmaan/fix/alttexttype
  * fix/alttexttype
  * Merge pull request #22 from Kunstmaan/fix/hidedeletednodes
  * Merge pull request #28 from Kunstmaan/fix_selectinputfield
  * fix/hidedeletednodes
  * you can select inputfield
  * Merge pull request #25 from Kunstmaan/fix/highlights
  * fix/highlights
  * fix ckeditor
  * Merge pull request #24 from Kunstmaan/feature/translations
  * Merge pull request #52 from Kunstmaan/fix_ckeditor
  * feature/translations
  * Merge branch 'fix_ckeditor' of github.com:Kunstmaan/KunstmaanAdminBundle into fix_ckeditor
  * fix ckeditor select url and image links
  * Merge pull request #23 from Kunstmaan/fix/searchname
  * fix/searchname
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Update README.md
  * Sigh….
  * Fixes
  * Add known issues
  * Add issues statement
  * Add dependencies
  * Formatting
  * Typo and add licensing information
  * Add some more context
  * Merge pull request #51 from Kunstmaan/fix_deletepage
  * Merge pull request #21 from Kunstmaan/fix_deletepage
  * Merge pull request #27 from Kunstmaan/fix_deletepage
  * delete pages and nodes, gone from tree and tables
  * deleted var set false in nodeconstructor and added to query in repository
  * deleted pages nog in tree for urlbrowser
  * Merge pull request #26 from Kunstmaan/fix_edit
  * double click on pagepart to edit
  * Merge pull request #23 from Kunstmaan/fix_ckeditor
  * Merge pull request #25 from Kunstmaan/fix_edit
  * debugcode delete
  * pagepart in editmode when adding fix
  * Update README.md
  * travis config
  * travis config
  * travis config
  * travis config
  * travis config
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanPagePartBundle
  * travis config
  * travis config
  * travis config
  * travis config
  * travis config
  * value of ckeditor stays on move pagepart
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminNodeBundle
  * travis config
  * Merge pull request #49 from Kunstmaan/feature/datafixtures
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanMediaBundle
  * travis config
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanViewBundle
  * travis config
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanMediaPagePartBundle
  * travis config
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminListBundle
  * Merge branch 'master' into feature/datafixtures
  * travis config
  * travis config
  * Update README.md
  * travis config
  * Ignored .DS_Store
  * Update README.md
  * Update README.md
  * Correct location of the vendor script
  * travis config
  * Update README.md
  * fix max-width imagepp
  * Update README.md
  * travis config
  * Update README.md
  * translations
  * default datafixtures added
  * Update README.md
  * Added the vendor script
  * Update README.md
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminListBundle
  * travis config
  * Update README.md
  * Added Travis framework
  * Added license
  * Added license
  * Added license
  * Added license
  * Added license
  * Added license
  * Added license
  * Added license
  * Merge pull request #7 from Kunstmaan/feature/search
  * Added Travis
  * Fix test
  * Complete setup
  * readme update
  * readme update
  * readme update
  * readme update
  * readme update
  * readme update
  * readme update
  * readme update
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminBundle
  * readme update
  * readme update
  * readme update
  * readme update
  * readme update
  * Merge pull request #48 from Kunstmaan/feature/linkbrowser
  * readme update
  * readme update
  * readme update
  * readme update
  * update readme
  * update readme
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanSearchBundle
  * composer.json
  * composer.json
  * composer.json
  * composer.json
  * composer.json
  * composer.json
  * composer.json
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminNodeBundle
  * composer.json
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminBundle
  * composer.json
  * Add the build instructions
  * Added the Travis information
  * Merge pull request #22 from Kunstmaan/fix_contentinfields
  * merge with master
  * tostring doesn't get set as value when adding pagepart
  * urlchooser fix
  * linkbrowser fix
  * Merge pull request #47 from Kunstmaan/feature/linkbrowser
  * linkbrowser in ckeditor
  * Merge pull request #46 from Kunstmaan/fix_pagesadminlist
  * Merge pull request #21 from Kunstmaan/feature/improvedlinkpp
  * feature/improvedlinkpp
  * edit pages from list and show only pages in current locale
  * Merge pull request #45 from Kunstmaan/fix/novalidation
  * fix/novalidation
  * Merge pull request #19 from Kunstmaan/fix_roles
  * Merge pull request #44 from Kunstmaan/fix_permissions
  * get roles from parentnode
  * get permissions of parent when adding new page
  * Merge pull request #43 from Kunstmaan/fix/exeptionlogger
  * Merge pull request #42 from Kunstmaan/feature/usercommands
  * Fix for spl_object_hash() expects parameter 1 to be object
  * Merge pull request #34 from Kunstmaan/fix_fixtures
  * translation fix
  * fix folder translations
  * kuma commands added to create user/group/role
  * Merge pull request #33 from Kunstmaan/fix/nomorevar_dump
  * Merge pull request #41 from Kunstmaan/fix/nomorevar_dump
  * removed var_dump
  * removed var_dump
  * Merge pull request #20 from Kunstmaan/fix/clickabletextinputs
  * make input fields clickable with draggable pageparts
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanMediaPagePartBundle
  * removed error_log
  * Merge pull request #40 from Kunstmaan/feature/roles
  * Support for roles in admin section / some refactoring / label inconsistencies fixed
  * Merge pull request #18 from Kunstmaan/fix/gettopnodes
  * fix gettopnodes
  * Merge pull request #17 from Kunstmaan/fix/gettopnodes
  * fix gettopnodes
  * Fix moved to AdminNodeBundle
  * Fix for User::getUserIds()
  * Merge pull request #39 from Kunstmaan/fix_menu
  * setChildrenAttributes instead of setAttributes
  * Merge pull request #38 from Kunstmaan/feature/logging
  * merge with master
  * Merge pull request #16 from Kunstmaan/feature/logging
  * logging
  * logging
  * Merge pull request #15 from Kunstmaan/fix/typo
  * fix/typo
  * Merge pull request #10 from Kunstmaan/fix/validhtmlmodal
  * Merge pull request #37 from Kunstmaan/feature/stickyfooterlogin
  * fix/validhtmlmodal
  * feature/stickyfooterlogin
  * exceptions
  * datetime fix
  * errorlog
  * Merge pull request #22 from Kunstmaan/fix/draftpreview
  * fixes draft preview
  * fixes draft preview
  * fixes draft preview
  * Merge pull request #36 from Kunstmaan/fix/pageadminformid
  * Merge pull request #19 from Kunstmaan/fix/pageadminformid
  * fix/pageadminformid
  * fix/pageadminformid
  * Merge pull request #35 from Kunstmaan/feature/seo
  * Merge pull request #14 from Kunstmaan/feature/seo
  * seoform fix
  * seotype fix
  * Merge pull request #21 from Kunstmaan/feature/seo
  * Merge pull request #34 from Kunstmaan/feature/seo
  * nodetranslation in resultarray
  * nodetranslation in resultarray
  * seo on pages
  * seo
  * Merge pull request #4 from Kunstmaan/feature/mediapopup
  * Merge pull request #32 from Kunstmaan/feature/mediapopup
  * feature/mediapopup
  * feature/mediapopup
  * Merge pull request #33 from Kunstmaan/fix/permissionroles
  * fix/permissionroles
  * Merge pull request #32 from Kunstmaan/fix/treesearch
  * Merge pull request #31 from Kunstmaan/fix/treesearch
  * fix treesearch
  * Merge branch 'master' into fix/treesearch
  * fix for treesearch
  * Merge pull request #29 from Kunstmaan/feature/cancel
  * Merge pull request #31 from Kunstmaan/feature/movenodes
  * Merge pull request #13 from Kunstmaan/feature/movenodes
  * move nodes in pages tree
  * fix nodegenerator
  * merge
  * move nodes in pages tree
  * config update
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanSearchBundle
  * Merge pull request #20 from Kunstmaan/feature/searches
  * merge
  * Merge pull request #30 from Kunstmaan/feature/searches
  * search filtering fix
  * adminlist fix
  * save search history
  * search history
  * searchestable in settings
  * Merge pull request #19 from Kunstmaan/fix_highlights
  * Merge pull request #5 from Kunstmaan/feature/searchparents
  * Merge pull request #12 from Kunstmaan/feature/search
  * fix get parents method in nodetranslation to also return own id
  * search also in searchpage parent
  * fix for highlights from content
  * Merge pull request #18 from Kunstmaan/fix/languagesearch
  * Merge pull request #4 from Kunstmaan/feature/parentsearch
  * Merge pull request #11 from Kunstmaan/feature/parentsearch
  * parent search fix
  * added parent search
  * parent search
  * Merge pull request #1 from Kunstmaan/feature/formsubmissionlist
  * Merge pull request #10 from Kunstmaan/feature/permissiononnodes
  * feature/formsubmissionlist
  * feature/permissionnodes
  * Merge pull request #17 from Kunstmaan/feature/searchpage
  * extra check to see if page is translated
  * Merge pull request #16 from Kunstmaan/feature/searchpage
  * Merge pull request #9 from Kunstmaan/feature/nodes
  * refresh van node in noderepository
  * check if query is set
  * Merge pull request #18 from Kunstmaan/feature/editmode
  * pageparts which is added in editmode
  * Merge pull request #28 from Kunstmaan/feature/permissiononnodes
  * cancel button in delete modal
  * cancel + delete-modal op pageparts
  * feature/permissiononnodes
  * Merge pull request #27 from Kunstmaan/feature/login
  * Merge branch 'feature/parentsearch' of github.com:Kunstmaan/KunstmaanAdminNodeBundle into feature/parentsearch
  * merge
  * Base for parent searching
  * taalswitcher not on loginscreen
  * Base for parent searching
  * Merge pull request #15 from Kunstmaan/feature/layoutfix
  * resources in demobundle
  * layout fix
  * initial commit
  * Merge pull request #29 from Kunstmaan/feature/errors
  * Merge pull request #26 from Kunstmaan/feature/cleanslug
  * Merge pull request #30 from Kunstmaan/feature/useclasslookup
  * Merge pull request #16 from Kunstmaan/feature/useclasslookup
  * Merge pull request #2 from Kunstmaan/feature/search
  * Merge pull request #3 from Kunstmaan/feature/useclasslookup
  * Merge pull request #12 from Kunstmaan/fix/languagesearch
  * Merge pull request #13 from Kunstmaan/feature/permissiononnodes
  * Merge pull request #8 from Kunstmaan/feature/permissiononnodes
  * feature/permissiononnodes
  * feature/useclasslookup
  * feature/useclasslookup
  * removed error_log
  * feature/useclasslookup
  * feature/permissiononnodes
  * feature/cleanslug
  * Make search language dependent
  * Make search language dependent
  * Merge pull request #15 from Kunstmaan/feature/urlchooser
  * Merge pull request #25 from Kunstmaan/feature/urlchooser
  * urlchooser in pagepart
  * urlchooser widget
  * thrown exception changes in entityNotFounDexception
  * throw httpexception if folder not fount
  * Merge pull request #28 from Kunstmaan/feature/layout
  * Merge pull request #24 from Kunstmaan/feature/pages
  * Merge pull request #10 from Kunstmaan/feature/searchpage
  * searchpage
  * layout update
  * Merge pull request #23 from Kunstmaan/fix/defaultindex
  * Fix for default index, this shouldn't be the pages overview
  * Merge pull request #14 from Kunstmaan/feature/pageparts
  * Merge pull request #22 from Kunstmaan/feature/pages
  * Merge pull request #3 from Kunstmaan/feature/newpageparts
  * Merge pull request #27 from Kunstmaan/feature/newpageparts
  * subfolder layout fix
  * ckeditor fix
  * chooser fixes + cleanup
  * chooser fixes
  * Merge branch 'feature/menu' into feature/newpageparts
  * deleted dutch translations
  * chooser update + delete french and dutch language
  * slide video image file pagepart update
  * view slide and video fix
  * setCurrent methode toegevoegd
  * current item fix in menu
  * merge master
  * merge master
  * only h2 in header pagepart
  * Merge branch 'feature/pageparts' of github.com:Kunstmaan/KunstmaanPagePartBundle into feature/pageparts
  * Merge pull request #13 from Kunstmaan/fix/cleanupimagepopupcode
  * Merge pull request #2 from Kunstmaan/feature/imagefield
  * Merge pull request #25 from Kunstmaan/fix/filechooser
  * Merge pull request #7 from Kunstmaan/fix/lookupnodes
  * Merge pull request #21 from Kunstmaan/feature/dialogbox
  * fix/cleanupimagepopupcode
  * feature/imagefield
  * fix filechooser for image field
  * fix for lookup nodes
  * dialogbox needed for media image field
  * Merge pull request #8 from Kunstmaan/feature/errorpages
  * javascriptfunctions for slide and video
  * slide and video pageparts
  * slide and video
  * Merge pull request #1 from Kunstmaan/feature/filepagepart
  * Merge pull request #12 from Kunstmaan/feature/pageparts
  * Merge pull request #24 from Kunstmaan/feature/filepagepart
  * imagechooser fix
  * javascript function name fix
  * javascript function name update
  * javascriptfunction update
  * javascript function name
  * file chooser + filesize
  * download pagepart
  * Merge pull request #23 from Kunstmaan/feature/imagepagepart
  * delete imagepagepart
  * imagepagepart first version
  * first commit
  * error page in viewbundle
  * Merge pull request #22 from Kunstmaan/fix/fixtureinterface
  * symfony changed the FixturesInterfaces, this is a fix
  * Merge pull request #7 from Kunstmaan/feature/highlights
  * Merge pull request #1 from Kunstmaan/feature/highlights
  * Added highlights
  * Added highlights to search
  * Merge pull request #6 from Kunstmaan/feature/pageservice
  * Merge pull request #11 from Kunstmaan/fix_classlookup
  * page can have a frontend view and service
  * Merge branch 'master' into fix_classlookup
  * fix class lookup pageparts and showing defaultview when there is no edit
  * Merge pull request #9 from Kunstmaan/feature/filters
  * Merge pull request #21 from Kunstmaan/feature/lists
  * Merge pull request #19 from Kunstmaan/feature/lists
  * Merge pull request #20 from Kunstmaan/feature/pages
  * Merge pull request #9 from Kunstmaan/feature/defaultpageparts
  * Merge pull request #10 from Kunstmaan/feature/pageparts
  * scroll to added pagepart + edit
  * scroll to added pagepart and edit
  * properties in pageparts tab
  * list filter update
  * list filter update
  * filter names option
  * Merge pull request #20 from Kunstmaan/feature/defaultpageparts
  * Merge pull request #6 from Kunstmaan/feature/properties
  * sequencenumber delete
  * image tostring update
  * imagepagepart
  * imagechosencallback defined or not
  * Merge pull request #18 from Kunstmaan/feature/footer
  * kunstmaan logo sharper
  * deleted orig
  * Merge branch 'master' into feature/defaultpageparts
  * Merge pull request #17 from Kunstmaan/feature/datepicker
  * datepicker script in head
  * fix for cut off button in action bar if only one button is used
  * Merge pull request #13 from Kunstmaan/feature/translations
  * Merge pull request #14 from Kunstmaan/feature/routing
  * Merge pull request #8 from Kunstmaan/feature/linkpagepart
  * Merge pull request #15 from Kunstmaan/feature/html
  * homepage redirects to pages
  * login only in english
  * javascript fix
  * Merge pull request #12 from Kunstmaan/feature/treesearch
  * treesearch in settings
  * Merge pull request #11 from Kunstmaan/feature/websitetitle
  * Merge pull request #10 from Kunstmaan/feature/treesearch
  * Merge pull request #19 from Kunstmaan/feature/treesearch
  * search in comment
  * search in comment
  * websitetitle in parameters
  * remove unneeded pagepart builder
  * Merge branch 'master' into feature/search
  * search page pagination
  * Merge pull request #5 from Kunstmaan/feature/nodeparents
  * removed dead code
  * Merge pull request #9 from Kunstmaan/feature/bugfixes
  * fix after merge
  * Merge branch 'master' into feature/nodeparents
  * fix to be able to click in pagepart input fields
  * nodemenuitem.getParent now returning the nodemenuitem instead of the parent node
  * Merge pull request #7 from Kunstmaan/feature/defaultpageparts
  * merge
  * Merge pull request #4 from Kunstmaan/feature/bugfixes
  * Merge branch 'master' into feature/bugfixes
  * fix for undefined method in adminlists
  * Merge pull request #3 from Kunstmaan/feature/search
  * get full slug in nodetranslation instead of nodemenuitem
  * nodetranslation full slug
  * find paginated
  * Merge pull request #2 from Kunstmaan/feature/search
  * Merge pull request #6 from Kunstmaan/feature/search
  * Merge pull request #4 from Kunstmaan/feature/search
  * Indexable use statement in nodetranslation
  * update tocpart for search
  * Merge branch 'feature/search' of github.com:Kunstmaan/KunstmaanViewBundle into feature/search
  * Merge pull request #8 from Kunstmaan/feature/classlookup
  * get classname
  * Merge pull request #5 from Kunstmaan/feature/contentpage
  * toc pagepart fix + ToTopPagePart
  * Merge pull request #3 from Kunstmaan/feature/cleanuptemplates
  * Merge pull request #7 from Kunstmaan/feature/layout
  * Merge pull request #4 from Kunstmaan/feature/linkpagepart
  * cleanup
  * Merge remote-tracking branch 'origin/master' into feature/cleanuptemplates
  * feature/cleanuptemplate
  * linkpage part + link selector
  * fix getPage in NodeMenuItem
  * layout fix
  * Bugfix user admin list - display users without roles
  * Merge pull request #18 from Kunstmaan/feature/imageeditor
  * Merge branch 'master' into feature/imageeditor
  * update aviary image editor
  * OK CSS for floated tree and content
  * link to page in search results
  * port in parameters
  * initial commit
  * fix for multiple pagepart contexts
  * fix pagepart editor when having multiple contexts
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanAdminBundle
  * fix adding new page
  * search nodemenu
  * fix header secondary nav
  * footer bottom css
  * Merge pull request #17 from Kunstmaan/feature/language
  * Merge pull request #2 from Kunstmaan/feature/language
  * Merge pull request #6 from Kunstmaan/feature/language
  * loginpage languagechooser update
  * languagechooser twig extension + routing updates
  * language requirements weg bij routing
  * language requirements weg bij routing
  * Merge pull request #5 from Kunstmaan/feature/multiplepagepartcontexts
  * Merge pull request #3 from Kunstmaan/feature/multiplepagepartcontexts
  * Merge branch 'master' into feature/multiplepagepartcontexts
  * Merge pull request #15 from Kunstmaan/feature/fixturesnamespace
  * Merge pull request #16 from Kunstmaan/feature/titletranslations
  * feature/multiplepagepartcontexts
  * translations title folders
  * Merge branch 'master' into feature/multiplepagepartcontexts
  * feature/multiplepagepartcontexts
  * folderfixtures namespace
  * Merge pull request #4 from Kunstmaan/feature/movenodes
  * Merge pull request #14 from Kunstmaan/feature/movenodes
  * Merge pull request #1 from Kunstmaan/feature/nodeversions
  * Merge pull request #3 from Kunstmaan/feature/nodeversions
  * move medianodes
  * media node move success
  * Merge pull request #1 from Kunstmaan/feature/nodeversions
  * TOC implementation according to template
  * move nodes media
  * Merge branch 'feature/defaultpageparts' of github.com:Kunstmaan/KunstmaanPagePartBundle into feature/defaultpageparts
  * changed needed for new nodeversions
  * reworked the nodes: now versions and translations are done in nodes.
  * changes needed to work with new nodeverions
  * Merge pull request #13 from Kunstmaan/feature/defaultpageparts
  * Merge pull request #2 from Kunstmaan/feature/defaultpageparts
  * backport of forms fix
  * identation fix
  * Merge pull request #1 from Kunstmaan/feature/languagechooser
  * current language in dropdown
  * Merge branch 'feature/defaultpageparts' of github.com:Kunstmaan/KunstmaanMediaBundle into feature/defaultpageparts
  * move nodes in tree script
  * first commit
  * first commit
  * first commit
  * Merge branch 'feature/defaultpageparts' of github.com:Kunstmaan/KunstmaanPagePartBundle into feature/defaultpageparts
  * fix for false hyperlink
  * fix for exception
  * Merge pull request #2 from Kunstmaan/feature/defaultpageparts
  * Merge branch 'master' into feature/defaultpageparts
  * Merge pull request #12 from Kunstmaan/feature/defaultpageparts
  * Merge pull request #11 from Kunstmaan/feature/cleanup
  * fix after wrong merge
  * Merge branch 'master' of github.com:Kunstmaan/KunstmaanPagePartBundle
  * copy pageparts
  * extra pageparts
  * extra pageparts
  * move nodes in tree
  * first commit
  * Merge branch 'feature/cleanup' into feature/defaultpageparts
  * changed the way pageparts are handled in the admin interface
  * Merge branch 'feature/cleanup' of github.com:Kunstmaan/KunstmaanMediaBundle into feature/cleanup
  * Basic image pagepart
  * link fix in ckeditor imagechooser
  * Merge pull request #10 from Kunstmaan/feature/cleanup
  * image selector for ckeditor
  * translation-fixes + add image url
  * added List pagepart
  * Merge pull request #1 from Kunstmaan/feature/newstyle
  * new style (Ibe)
  * Merge pull request #9 from Kunstmaan/feature/cleanup
  * Merge pull request #8 from Kunstmaan/feature/filters
  * dropdown to add media in folder
  * add objects dropdown choice field
  * Merge pull request #7 from Kunstmaan/feature/filters
  * Merge pull request #8 from Kunstmaan/feature/cleanup
  * sidebar div fix
  * datePicker
  * Merge pull request #7 from Kunstmaan/feature/cleanup
  * Merge pull request #6 from Kunstmaan/feature/filters
  * Merge branch 'feature/cleanup' of github.com:Kunstmaan/KunstmaanMediaBundle into feature/cleanup
  * delete image overlay fix
  * add folder type documentation
  * Merge branch 'feature/cleanup' of github.com:Kunstmaan/KunstmaanMediaBundle into feature/cleanup
  * addnewfoldertype.md first commit
  * update addmedia.md
  * addmedia.md first commit
  * list translations
  * filter translations
  * actions translation
  * add multiple objects to one list
  * medialists add multiple types to one list
  * update readme.md
  * cleanup
  * no sub text + translations
  * media datafixtures
  * Merge pull request #5 from Kunstmaan/feature/filters
  * translation fixes
  * folders + media cleanup
  * folderfactory
  * media cleanup for folders
  * folders + media fixes
  * queryparams in filtering and paging
  * adding query parameters
  * medialist fix + folder
  * first folders
  * tablename updates
  * Merge pull request #4 from Kunstmaan/feature/filters
  * Merge pull request #6 from Kunstmaan/feature/cleanup
  * media tree
  * filter icons
  * Merge branch 'feature/cleanup' of github.com:Kunstmaan/KunstmaanMediaBundle into feature/cleanup
  * Merge branch 'feature/filters' of github.com:Kunstmaan/KunstmaanAdminListBundle into feature/filters
  * basis voor draganddrop upload
  * deleted default index
  * twig updates
  * twig updates
  * Merge pull request #3 from Kunstmaan/feature/filters
  * Merge pull request #5 from Kunstmaan/feature/cleanup
  * datefilter twig update
  * list updates
  * medialists fixes + translations
  * datefilter update
  * stringfilter update + first date filter
  * menu translation
  * locale in url
  * translations update
  * translations
  * Merge branch 'feature/cleanup' of github.com:Kunstmaan/KunstmaanMediaBundle into feature/cleanup
  * Merge pull request #4 from Kunstmaan/feature/medialists
  * Merge pull request #2 from Kunstmaan/feature/parameters
  * gitignore
  * videocontroller fix
  * methods for getting pageparts
  * algemene cleanup mediabundle
  * lists fix
  * lists in file, slide and video galleries
  * Merge branches 'feature/gallerytree' and 'feature/medialists' into feature/gallerytree
  * delete - are you sure?
  * extraparams in route
  * tree fixes
  * tree van galleries
  * edit files + images delete in tableview
  * slide edit fix + video edit
  * edit gallery fix
  * add gallery to gallery fix + edit slides
  * routing fix
  * redirect + routing fixes
  * adminlist implement at filegalleries
  * uploaddir naar config ipv routing
  * routering in annotations
  * edit galleries
  * Merge pull request #1 from Kunstmaan/feature/sort_filter
  * Merge pull request #3 from Kunstmaan/feature/video
  * Merge branch 'master' into feature/video
  * Merge branch 'master' into feature/video
  * Merge pull request #2 from Kunstmaan/feature/edit-delete
  * Merge branch 'master' into feature/edit-delete
  * Merge pull request #1 from Kunstmaan/feature/MediaMenu
  * made sort, filter and delete functional
  * delete + cleanup
  * adding vimeo, youtube and dailymotion
  * media menu adaptor
  * adding imagine to the routing file
  * info about adding imagine + gaufrette in appkernel and autoload
  * AdminBundle refactoring
  * fix wrong filenames
  * first real commit
  * first commit
  * Update README.md
  * slidegallery fixtures fix
  * first real commit
  * first commit
  * Update README.md
  * README.md
  * KunstmaanKMediaBundle -> KunstmaanMediaBundle
  * initial commit 2
  * initial commit
