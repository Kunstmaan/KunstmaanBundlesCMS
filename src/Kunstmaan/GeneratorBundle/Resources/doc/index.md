KunstmaanGeneratorBundle by Kunstmaan
=================================

This bundle provides the following generation options :

## Bundle

The bundle generator is based on the bundle generator of the SensoGeneratorBundle. We modified it with predetermined options : YAML for configurations files and no structure files generation.

### Command

You use the bundle generating with the following command. You will be prompted for the 'namespace', 'dir' and 'bundle-name' parameters.

```
app/console kuma:generate:bundle
```

## AdminList

When you created an Entity and you want to generate a standard working KunstmaanAdminList for that Entity, this generated will allow you to do so.

This generator will generate the following files in the bundle where the Entity class is located.

* AdminListConfguration in /AdminList
* AdminListController in /Controller
* AdminType in /Form

All three classes will be generated from skeleton classes and using Twig generation it will generated the code based on the Fields which exist in the entity's class.

### Command

The 'entity' parameter is required in order to generated the AdminList files based on that Entity.

```
app/console kuma:generate:adminlist --entity=Bundle:Entity
```

## Website

Using this generator you can generate a default website using the Kunstmaan bundles. This generated website will result in a working CMS and frontend website which will display the possibilities of the CMS and will give you a starting template to build your site on.

### Command

The 'namespace' parameter is required and will determine in which bundle the files will be generated.

The 'prefix' parameter is optional and will allow you to add a prefix to all table names used by the generated classes. When generating more than one website, you can prevent the websites using the same tables in the database

```
app/console kuma:generate:default-site --namespace=Namespace\NamedBundle --prefix=tableprefix_
```