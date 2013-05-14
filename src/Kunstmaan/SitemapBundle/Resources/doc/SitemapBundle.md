# SitemapBundle

After installing this bundle, you can go to the '/en/sitemap' url on your website, a sitemap XML based on the [Sitemap protocol](http://www.sitemaps.org/protocol.html) will be generated.

You can hide pages from the sitemap by implementing the HiddenFromSitemap interface, this interface will allow you the hide the page and/or its children from the sitemap.

## Twig extension

This bundle also supplies two new twig extensions, both methods accept a [NodeMenuItem](https://github.com/Kunstmaan/KunstmaanNodeBundle/blob/master/Helper/NodeMenuItem.php) as parameter and will return a boolean.

```
    hide_from_sitemap(nodemenuitem)
```

```
    hide_children_from_sitemap(nodemenuitem)
```