# KunstmaanMultiDomainBundle

Sometimes you want to attach multiple domains to your website or maybe even manage multiple sites using the
same basic CMS. This bundle will allow you to setup the bundles CMS to do just that. You can define multiple
hosts, specify if they are single or multi language, and assign a root page for them (using the internal name
of the page).

## Usage

### Before you start

Your site *has* to be defined as a multilanguage site in parameters.yml.


### Modify app/AppKernel.php

You have to add the MultiDomainBundle in the registerBundles function :

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Kunstmaan\MultiDomainBundle\KunstmaanMultiDomainBundle(),
        );
        ...
    }
    ...
}
```

### Include the routing configuration in your main routing.yml

```yml
KunstmaanMultiDomainBundle:
    resource: "@KunstmaanMultiDomainBundle/Resources/config/routing.yml"
    requirements:
        _locale: "%requiredlocales%"
```

### Add the multi domain configuration in your config.yml :

```yml
kunstmaan_multi_domain:
    hosts:
        main_nl_be:
            host: demo-nl.dev.kunstmaan.be
            aliases:
              - demo-nl.prod.kunstmaan.be
            type: single_lang
            default_locale: nl_BE
            locales:
                - { uri_locale: 'nl', locale: 'nl_BE' }
            root: homepage

        main_fr_be:
            host: demo-fr.dev.kunstmaan.be
            aliases:
              - demo-fr.prod.kunstmaan.be
            type: single_lang
            default_locale: fr_BE
            locales:
                - { uri_locale: 'fr', locale: 'fr_BE' }
            root: homepage

        subsite_be:
            host: subsite.dev.kunstmaan.be
            aliases:
              - subsite.prod.kunstmaan.be
            type: multi_lang
            default_locale: nl_BE
            locales:
                - { uri_locale: 'nl', locale: 'nl_BE' }
                - { uri_locale: 'fr', locale: 'fr_BE' }
            root: subsite_homepage
            extra:
                foo: bar
```

This will define 2 node trees for 3 domains.

The first tree is mapped to 2 single language domains, the second is mapped as a multi language site.

*Note*: You will not be able to use a locale as both single and multi language for the same root.


### Enable the multi domain logout handler in security.yml

If you intend to use the host override functionality, you have to enable the ```kunstmaan_multi_domain.host_override_cleanup```
logout handler in app/conf/security.yml. If you don't do this the host override will be active until you close the
current browser session.

```
    firewalls:
        main:
            pattern: ^/([^/]*)/admin
            form_login:
                login_path: fos_user_security_login
                check_path: fos_user_security_check
                provider: fos_userbundle
            logout:
                path:   fos_user_security_logout
                target: KunstmaanAdminBundle_homepage
                handlers: ['kunstmaan_multi_domain.host_override_cleanup']
            anonymous:    true
            remember_me:
                key:      %secret%
                lifetime: 604800
                path:     /
                domain:   ~
```


### Accessing the back-end

If you were used to working with the older versions of the bundles CMS there's one thing to note : the admin
area will now be accessible *only* by using the locales you specified in the multi domain configuration
regardless of them being single or multilanguage.

ie. To access the admin area for subsite.dev.kunstmaan.be you would have to go to :

http://subsite.dev.kunstmaan.be/nl_BE/admin/ or http://subsite.dev.kunstmaan.be/fr_BE/admin/

*Note*:
If you login and the root page for your site does not exist yet, you will see the complete site tree for all
sites you are managing. As super administrator you can add a homepage by clicking on the "Add homepage" button
situated above the Pages list.

Homepages should implement the HomePageInterface in order to be visible in the home page type selection.

In the back-end you will be able to switch to the different domains.


## Extra Twig functions

The following extra Twig functions are available when you enable the multi domain bundle :

- ```get_multi_domain_hosts()``` - returns the hosts that are defined in the multi domain configuration
- ```get_current_host()``` - returns the current host (either the real one or the current override)
