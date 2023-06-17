UPGRADE FROM 6.2 to 6.3
========================

General
-------

- The supported Symfony version is 5.4.
- Make sure to switch your swiftmailer email config to symfony mailer config after upgrading to avoid breaking email sending from the CMS. 
  To avoid any BC breaks the `Kunstmaan\FormBundle\Helper\FormMailer` still uses swiftmailer as the default mailer. 
  If you want to supress the deprecation warning, alias `@kunstmaan_mailer` to the symfony mailer service. 
  In 7.0 `@kunstmaan_mailer` will be removed and the symfony mailer will be used by default.
- We replaced the abandoned `twig/extensions` packages by the replacement sub packages. If you use any of the twig filters/functions
  of the `twig/extensions` package, make sure to require the necessary replacement package in your project.
- Removed unused `egulias/email-validator` dependency. If you use this in your project, add `egulias/email-validator` to your project composer.json.

AdminBundle
-----------

- The `Kunstmaan\AdminBundle\Service\AuthenticationMailer\SwiftmailerService` authentication mailer service is deprecated, use `Kunstmaan\AdminBundle\Service\AuthenticationMailer\SymfonyMailerService` instead.
- The default value of `kunstmaan_admin.authentication.mailer.service` will change to `Kunstmaan\AdminBundle\Service\AuthenticationMailer\SymfonyMailerService` in 7.0.

CookieBundle
------------

- The `Kunstmaan\CookieBundle\Controller\LegalController::legalPageAction` method is deprecated and will be removed. Is replaced by `Kunstmaan\CookieBundle\ViewDataProvider\LegalPageViewDataProvider`.

FormBundle
----------

- Not passing the required services to `Kunstmaan\FormBundle\Helper\FormHandler::__construct` is deprecated and those parameters will be required in 7.0. Injected the required services in the constructor instead.

MultidomainBundle
-----------------

- The `Kunstmaan\MultiDomainBundle\Helper\HostOverrideCleanupHandler` class is deprecated and is replaced by the `Kunstmaan\MultiDomainBundle\EventSubscriber\LogoutHostOverrideCleanupEventSubscriber` subscriber with the new authentication system.

RedirectBundle
--------------

- The redirect-bundle has now some improved logic in the router itself. Using the old router logic is deprecated and the new will be the default in 7.0.
  To enable the new and improved router, set the `kunstmaan_redirect.enable_improved_router` config to `true`.

  This improved router has the following changes:
  - Huge speed improvement with larger sets of cms redirects. See [#3239](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/3239)
  - Each redirect path in the database should start with a `/` so the improved redirect lookup will work. When migrating an existing website
    to the new router setup, execute the following queries to prefix all redirects.
    ```sql
    UPDATE kuma_redirects SET origin = CONCAT('/', origin) WHERE origin NOT LIKE '/%';
    UPDATE kuma_redirects SET target = CONCAT('/', target) WHERE target NOT LIKE '/%' AND target NOT LIKE '%://%';
    ```
  - Changed the wildcard redirect to be more correct for all usecases. See this comparisson in behaviour

Old:

| Origin  | Target  | Request url    | Result           |
|---------|---------|----------------|------------------|
| /news/* | /blog   | /news/article1 | /blog/article1   |
| /news/* | /blog/* | /news/article1 | /blog/*/article1 |
| /news   | /blog   | /news          | /blog            |

New:

| Origin  | Target  | Request url    | Result         |
|---------|---------|----------------|----------------|
| /news/* | /blog   | /news/article1 | /blog          |
| /news/* | /blog/* | /news/article1 | /blog/article1 |
| /news   | /blog   | /news          | /blog          |


UtilitiesBundle
---------------

- The `Kunstmaan\UtilitiesBundle\Helper\UrlTransactionNamingStrategy` class is deprecated and will be removed in 7.0. If you need the class, copy and implement it in your project.
