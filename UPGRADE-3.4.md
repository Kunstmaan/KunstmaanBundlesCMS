# UPGRADE FROM 3.3 to 3.4

## DomainConfiguration was moved from KunstmaanNodeBundle to KunstmaanAdminBundle (BC breaking)

We had to do a BC breaking refactoring of the DomainConfiguration for sites that don't use the KunstmaanNodeBundle. This will only impact you if you already
used/injected the domain configuration services in your own projects.

Upgrade instructions :

- replace all occurrences of ```kunstmaan_node.domain_configuration``` with ```kunstmaan_admin.domain_configuration```
- replace all references to ```Kunstmaan\NodeBundle\Helper\DomainConfigurationInterface``` in your code with ```Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface```

## Updates to Social and Seo tabs of pages.

We updated and simplified the seo an social tab of pages. Also support for Twitter cards has been added.
Therefor we made several changes in the seo table of the database. 
You should generate a migration and run it on all your databases (dev/production).

```
app/console doctrine:migrations:diff
app/console doctrine:migrations:migrate
```
