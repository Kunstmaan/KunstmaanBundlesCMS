# ArticleBundle

## Generating a new Article section

Generate the classes for an overview and its detail pages. The detail page is a content page with pageparts and a summary text field. The overview page contains a paginated list of its articles and shows for each article its title and summary and will link to the article page for its full content.

The Articles CRUD can be found under 'Modules' after generation. It's required you create an overview page before you can add new articles. Add your generated overview page as a possible child to any page in your website.

### Command

The 'namespace' parameter is required and will determine in which bundle the files will be generated.

The 'entity' parameter is required in order to generated the class names. Most used entity names are "News", "Press", ...

The 'prefix' parameter is optional and will allow you to add a prefix to all table names used by the generated classes.

```
bin/console kuma:generate:article --namespace=Namespace\\NamedBundle --entity=Entity --prefix=tableprefix_
```

### Generated files

Assuming we generated articles using the following parameters :

```
bin/console kuma:generate:article --namespace=Kunstmaan\\NewsBundle --entity=news --prefix=news_
```

The following files will be generated :

* Kunstmaan/NewsBundle/AdminList/News/NewsAuthorAdminListConfigurator.php
* Kunstmaan/NewsBundle/AdminList/News/NewsPageAdminListConfigurator.php
* Kunstmaan/NewsBundle/Controler/News/NewsAuthorAdminListController.php
* Kunstmaan/NewsBundle/Controler/News/NewsPageAdminListController.php
* Kunstmaan/NewsBundle/Entity/News/NewsAuthor.php
* Kunstmaan/NewsBundle/Entity/News/NewsOverviewPage.php
* Kunstmaan/NewsBundle/Entity/News/NewsPage.php
* Kunstmaan/NewsBundle/Form/News/NewsAuthorAdminType.php
* Kunstmaan/NewsBundle/Form/News/NewsPageAdminType.php
* Kunstmaan/NewsBundle/Helper/Menu/NewsMenuAdaptor.php
* Kunstmaan/NewsBundle/PagePartAdmin/News/NewsOverviewPagePartAdminConfigurator.php
* Kunstmaan/NewsBundle/PagePartAdmin/News/NewsPagePartAdminConfigurator.php
* Kunstmaan/NewsBundle/Repository/News/NewsAuthorRepository
* Kunstmaan/NewsBundle/Repository/News/NewsPageRepository
* Kunstmaan/NewsBundle/Resources/config/routing.yml
* Kunstmaan/NewsBundle/Resources/config/services.yml
* Kunstmaan/NewsBundle/views/AdminList/NewsPageAdminList/list.html.twig
* Kunstmaan/NewsBundle/views/News/NewsOverviewPage/view.html.twig
* Kunstmaan/NewsBundle/views/News/NewsPage/view.html.twig

### Entities

#### ArticleOverviewPage

The overview page can contain PageParts. The paginated list of articles will be shown under these PageParts. The articles will be shown by 10 per page and shows a teaser for each article containing the title, which will link to the article, and its summary.

#### ArticlePage

The ArticlePage is the full content page of the article and will show all the PageParts on this page. The summary will now be shown.

The ArticlePage also has a field to select its ArticleAuthor.

#### ArticleAuthor

The article author has a name and a link.
