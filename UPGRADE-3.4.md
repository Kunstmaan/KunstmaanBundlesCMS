# UPGRADE FROM 3.3 to 3.4

## DomainConfiguration was moved from KunstmaanNodeBundle to KunstmaanAdminBundle (BC breaking)

We had to do a BC breaking refactoring of the DomainConfiguration for sites that don't use the KunstmaanNodeBundle. This will only impact you if you already
used/injected the domain configuration services in your own projects.

Upgrade instructions :

- replace all occurrences of ```kunstmaan_node.domain_configuration``` with ```kunstmaan_admin.domain_configuration```
- replace all references to ```Kunstmaan\NodeBundle\Helper\DomainConfigurationInterface``` in your code with ```Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface```

## Changed AdminList edit buttons from images to icons (BC breaking).

The feature for adding custom buttons to admin list and item edit page via configurator was acting differently. In the twig template of list it was printed out as icon class while on the edit page - as an image.

If you are using this feature, please define your button icons as classes, not as the whole image. However, if you still want to use images, feel free to overwrite the template `src/Kunstmaan/AdminListBundle/Resources/views/Default/add_or_edit.html.twig`.

More info could be found in the [pull request](https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/755).

## Updates to Social and Seo tabs of pages.

We updated and simplified the seo an social tab of pages. Also support for Twitter cards has been added.
Therefor we made several changes in the seo table of the database. 
You should generate a migration and run it on all your databases (dev/production).

```
app/console doctrine:migrations:diff
app/console doctrine:migrations:migrate
```
