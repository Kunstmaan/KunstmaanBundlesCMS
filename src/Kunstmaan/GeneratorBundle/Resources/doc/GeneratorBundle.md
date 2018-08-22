KunstmaanGeneratorBundle by Kunstmaan
=================================

This bundle provides the following generation options :

## Bundle

The bundle generator is based on the bundle generator of the [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle). We modified it with predetermined options : YAML for configurations files and no structure files generation.

### Command

You use the bundle generating with the following command. You will be prompted for the 'namespace', 'dir' and 'bundle-name' parameters.

```
bin/console kuma:generate:bundle
```

## Entity

This entity generator will generate an entity which extends the [AmdinBundle](https://github.com/Kunstmaan/KunstmaanAdminBundle)'s [AbstractEntity](https://github.com/Kunstmaan/KunstmaanAdminBundle/blob/master/Entity/AbstractEntity.php) in your Entity folder.

The command will ask you if you want to create an empty Repository class and an AdminList. When you also create the repository, the repository will be generated in the bundle's Repository folder keeping the folder structure of your Entity. When also opting to generate an AdminList, the kuma:generate:adminlist command will be triggered, see below for more information.

This generator is based on the SensioBundleGenerator's GenerateDoctrineEntity, for more information, see their [documentation](https://github.com/sensio/SensioGeneratorBundle/blob/master/Resources/doc/commands/generate_doctrine_entity.rst). The format option has been removed from this generator.

### Command

```
bin/console kuma:generate:entity
```

## AdminList

When you created an Entity and you want to generate a standard working [KunstmaanAdminList](https://github.com/Kunstmaan/KunstmaanAdminListBundle) for that Entity, this generated will allow you to do so.

This generator will generate the following files in the bundle where the Entity class is located.

* AdminListConfguration in /AdminList
* AdminListController in /Controller
* AdminType in /Form

All three classes will be generated from skeleton classes and using Twig generation it will generated the code based on the Fields which exist in the entity's class.

### Command

The 'entity' parameter is required in order to generated the AdminList files based on that Entity.

```
bin/console kuma:generate:adminlist --entity=Bundle:Entity
```

## Website

Using this generator you can generate a default website using the Kunstmaan bundles. This generated website will result in a working CMS and frontend website which will display the possibilities of the CMS and will give you a starting template to build your site on.

### Command

The 'namespace' parameter is required and will determine in which bundle the files will be generated.

The 'prefix' parameter is optional and will allow you to add a prefix to all table names used by the generated classes. When generating more than one website, you can prevent the websites using the same tables in the database.

```
bin/console kuma:generate:default-site --namespace=Namespace\NamedBundle --prefix=tableprefix_
```

## Search Page

Generate a search page for your website which will search the pages in the index created by the [KunstmaanNodeSearchBundle](https://github.com/Kunstmaan/KunstmaanNodeSearchBundle). Only published nodes will be indexed.

The search page holds by default 10 results per page and each result shows the page title, a highlighted best fragment and will link to the page. If your page is Taggable ([fpn/doctrine-extensions-taggable](https://github.com/FabienPennequin/DoctrineExtensions-Taggable)), the tags will be available as aggregations to filter on.

### Command

The 'namespace' parameter is required and will determine in which bundle the files will be generated.

The 'prefix' parameter is optional and will allow you to add a prefix to all table names used by the generated classes.

```
bin/console kuma:generate:searchpage --namespace=Namespace\NamedBundle --prefix=tableprefix_
```

## Article : Overview and detail pages

Generate the classes for an overview and its detail pages. The detail page is a content page with pageparts and a summary text field. The overview page contains a paginated list of its articles and shows for each article its title and summary and will link to the article page for its full content.

### Command

The 'namespace' parameter is required and will determine in which bundle the files will be generated.

The 'entity' parameter is required in order to generated the class names. Most used entity names are "News", "Press", ...

The 'prefix' parameter is optional and will allow you to add a prefix to all table names used by the generated classes.

```
bin/console kuma:generate:article --namespace=Namespace\NamedBundle --entity=Entity --prefix=tableprefix_
```

## Page

Using this command you can generate a new page. This command will generate an entity -and form type class.
It will also configure the page template and page sections for the created page.

### Command

The 'prefix' parameter is optional and will allow you to add a prefix to all table names used by the generated classes.

```
bin/console kuma:generate:page --prefix=tableprefix_
```

## PagePart

Using this command you can generate a new page part that can be used in page sections. This command will
generate an entity class, a form type class and a twig template. It will also update the yml section
configuration file(s), so that the page part can be used in the section(s).

### Command

The 'prefix' parameter is optional and will allow you to add a prefix to all table names used by the generated classes.

```
bin/console kuma:generate:pagepart --prefix=tableprefix_
```
