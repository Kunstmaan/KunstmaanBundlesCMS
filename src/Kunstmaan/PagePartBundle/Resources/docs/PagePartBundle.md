# PagePartBundle

## Pageparts
The KunstmaanPagePartBundle forms the basis of our content management framework. A page built using a composition of blocks names pageparts. These pageparts allow you to fully separate the data from the presentation so non-technical webmasters can manage the website. Every page can have it's own list of possible pageparts, and pageparts are easy to create for your specific project to allow for rapid development.

## Page part configurations
For each page you can configure multiple contexts which pageparts in it. (For example "main", "banners", "right column").

```PHP
public function getPagePartAdminConfigurations()
{
    return array("BoleroOpenWebsiteBundle:news");
}
```
