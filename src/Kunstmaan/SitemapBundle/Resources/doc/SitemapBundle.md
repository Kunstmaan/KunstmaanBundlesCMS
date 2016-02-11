# SitemapBundle

After installing this bundle, you can go to the '/en/sitemap' url on your website, a sitemap XML based on the [Sitemap protocol](http://www.sitemaps.org/protocol.html) will be generated.

You can hide pages from the sitemap by implementing the HiddenFromSitemap interface, this interface will allow you the hide the page and/or its children from the sitemap.

## XML

Once the bundle is installed, you will be able to view the generated sitemap XML on the '/en/sitemap.xml' route.

## Page

The bundle also has a SitemapPage which can be added to your website, simply add the SitemapPage as a possible child to one of your pages :

```PHP
    public function getPossibleChildTypes()
    {
        return array(
            array(
                'name' => 'Sitemap Page',
                'class'=> "Kunstmaan\SitemapBundle\Entity\SitemapPage"
            )
        );
    }
```
And override its template by copying the view.html.twig of the SitemapPage to the the following location 'app/Resources/KunstmaanSitemapBundle/views/SitemapPage'.

```twig
{% extends 'YourWebsiteBundle:Page:layout.html.twig' %}

{% block content %}
    <ul class="sitemap">
        {% if nodemenu is defined %}
            {% for topNode in nodemenu.getTopNodes() %}
                {% for node in topNode.getChildren() %}
                    {{ include('KunstmaanSitemapBundle:SitemapPage:entry.html.twig', {'entry' : node})  }}
                {% endfor %}
            {% endfor %}
        {% endif %}
    <ul>
{% endblock %}
```

## Twig extension

This bundle also supplies two new twig extensions, both methods accept a [NodeMenuItem](https://github.com/Kunstmaan/KunstmaanNodeBundle/blob/master/Helper/NodeMenuItem.php) as parameter and will return a boolean.

```
hide_from_sitemap(nodemenuitem)
```

```
hide_children_from_sitemap(nodemenuitem)
```
