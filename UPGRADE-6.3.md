UPGRADE FROM 6.2 to 6.3
========================

General
-------

- The supported Symfony version is 5.4.

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
