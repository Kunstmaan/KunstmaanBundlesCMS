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

## Removed keywords meta tags

Most of the major search engines don't use the keywords meta tag anymore.(see
http://googlewebmastercentral.blogspot.be/2009/09/google-does-not-use-keywords-meta-tag.html as example)
Therefore we decided to remove the fields because it added to the complexity of the CMS and gave our customers a false sense of SEO optimalisation.
If you do want to use keywords, you can do so by extending the SEOBundle with your own class and adding the needed property.

## Add extra method to the DomainConfigurationInterface.

The `getLocalesExtraData` method was added. If you have created your own DomainConfiguration class you will need
to implement the extra method.

This extra method will allow you to retrieve the extra parameters defined for each domain locale.

```
kunstmaan_multi_domain:
    hosts:
        general:
            host: domain.com
            type: multi_lang
            default_locale: en
            locales:
                - { uri_locale: 'en', locale: 'en', extra: {country: 'UK', code: 'abc'} }
                - { uri_locale: 'fr', locale: 'fr', extra: {country: 'FR', code: 'xyz'} }
```
