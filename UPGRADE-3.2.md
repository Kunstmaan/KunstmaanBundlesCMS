# UPGRADE FROM 3.1 to 3.2

## Manual upgrade needed in app/routes to refactor sitemaps

### WHAT
This feature generates one central sitemap.xml file at your document root to define a sub sitemap for each language available.
You can now have up to 50000 urls for each language you define.
As a bonus, you only have to register the sitemap index (/sitemap.xml) at the search engines to register all available languages.

### HOW
In order to upgrade, you need a new route in your app/config/routes.yml.
```yml
KunstmaanSitemapBundle_sitemapIndex:
    resource: "@KunstmaanSitemapBundle/Controller/SitemapController.php"
    type:     annotation
```