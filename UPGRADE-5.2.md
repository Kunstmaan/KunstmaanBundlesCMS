UPGRADE FROM 5.1 to 5.2
=======================

General
-------

 * We don't depend on the `symfony/symfony` package anymore, instead the individual `symfony/*` packages are added as dependencies.
   If your code depends on other symfony packages than the ones we require, add them to your project `composer.json`.
 * The `symfony/monolog-bundle` package was removed as it was no dependency of the kunstmaan cms. If you use this in your project, add the `"symfony/monolog-bundle": "~2.8|~3.0"` constraint to your project `composer.json`.
