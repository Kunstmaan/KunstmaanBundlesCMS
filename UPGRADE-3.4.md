# UPGRADE FROM 3.3 to 3.4

## DomainConfiguration was moved from KunstmaanNodeBundle to KunstmaanAdminBundle (BC breaking)

We had to do a BC breaking refactoring of the DomainConfiguration for sites that don't use the KunstmaanNodeBundle. This will only impact you if you already
used/injected the domain configuration services in your own projects.

Upgrade instructions :

- replace all occurrences of ```kunstmaan_node.domain_configuration``` with ```kunstmaan_admin.domain_configuration```
- replace all references to ```Kunstmaan\NodeBundle\Helper\DomainConfigurationInterface``` in your code with ```Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface```
