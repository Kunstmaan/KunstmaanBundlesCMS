KunstmaanCookieBundle
=====================

The Kunstmaan Cookie Bundle provides a cookie bar; 
detailed pop-up window and a similar page 
explaining each type of cookie used on the website.

All provide the ability to accept or decline certain cookies.

# Enabling the bundle

## Add to Appkernel.php
## Add to routing.yml

```
KunstmaanCookieBundle:
    resource: "@KunstmaanCookieBundle/Resources/config/routing.yml"
    prefix:   /{_locale}/
    requirements:
      _locale: "%requiredlocales%"
```

## Two ways to view the cookie implementation

1: Login into the CMS

2: enable the cookie bundle under Settings -> Cookie configuration 

# Importing the cookie bundle

## Show cookie on page
Add following block to the main layout of your website

```
{# Kuma Cookie Bar #}
{% block kumacookiebar %}
    <kuma-cookie-bar></kuma-cookie-bar>
{% endblock %}
```

## CSS: First Method
Apply all CSS by importing the legal.scss file 
into the vendors file of your project

```
@import "vendor/kunstmaan/cookie-bundle/Resources/ui/scss/legal";
``` 

## CSS: Second method
Import the Kunstmaan Cookie Bundle variables and imports to be overridden. 
Copy the files at the following path to your project folder.

vendor/kunstmaan/cookie-bundle/Resources/ui/scss/config/_variables.scss
vender/kunstmaan/cookie-bundle/Resources/ui/scss/config/_legal-imports.scss

Alter variables and comment imports to fit the project's styling.

## Javascript
### Via ES Module pattern
```
import '<web or public>/bundles/kunstmaancookie/js/';
```
### Via buildtool
You can include the compiled version in your buildtool - or directly in a template - via the following URL:
```
<web or public>/bundles/kunstmaancookie/js/cookie-bundle.min.js
```

### Global methods
The CookieBundle exposes some utility methods that you can include in your project.
```
import {getKmccCookies} from '<ROOR_DIR>/<web or public>/bundles/kunstmaancookie/js/'; // Returns the settings of all the cookies.
import {hasAllowedDatalayers} from '<ROOR_DIR>/<web or public>/bundles/kunstmaancookie/js/'; // Shorthand to check if you're allowed to use dataLayers in this
 project.
import {asyncDomInitiator} from '<ROOR_DIR>/<web or public>/bundles/kunstmaancookie/js/'; // This is to initialize async inserted Cookie Bundle Components. Expects 1 param: an object like the following: {nodeTree: <HTMLElement>}
``` 

Both these methods are also available on the global scope for projects that do not have bundlers:
```
window.kmcc.getKmccCookies(); 
window.kmcc.hasAllowedDatalayers(); 
window.kmcc.asyncDomInitiator({
    nodeTree: <HTMLElement>
});
```

# Commands

## Copy Cookiebar Resources to your project

Default command:
```
php bin/console kuma:generate:legal --prefix foo_ --demosite
``` 
or, if you have previously generated files and wish to override them:
php bin/console kuma:generate:legal --prefix will_ --demosite --overrideFiles


## Migrate

```
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
``` 


## Generate fixtures
 
```
php bin/console d:f:l --group=cookie-bundle --append
``` 

## Add the optin pagepart to your form pages. 
We added an extra LegalOptInPagePart that behaves like a checkbox but adds the extra of a link to the privacy policy directly.

The pagepart will be generated in your bundle and you will have to include it in your pagepart configuration yaml file.

```
    - { name: 'Opt In', class: Foo\WebsiteBundle\Entity\PageParts\LegalOptInPagePart }
```

## Easily generate links for your project that open the cookie modal:

Add by using this snippet you can easily add the necessary links to your project in custom places like a footer:

```
{% if is_granted_for_cookie_bundle(app.request) %}
    {% set legalLinks = ['legal_privacy_policy', 'legal_cookie_preferences', 'legal_contact'] %}
    {% for internalName in legalLinks %}
        {% set node = nodemenu.getNodeByInternalName(internalName) %}
        {% if (node is not null) %}
            -
            <a data-target="{{ internalName }}" data-url="{{ path('kunstmaancookiebundle_legal_modal', {'internal_name': internalName}) }}"
                class="sub-footer__info__link js-kmcc-extended-modal-button">
                {{ node.title }}
            </a>
        {% endif %}
    {% endfor %}
{% endif %}
```

## Overriding translations

If you want to override the translations of the cookie bundle, you need to add the following configuration in your config.yml 
beneath the existing kunstmaan_translator config.

```
kunstmaan_translator:
    default_bundle: custom
    bundles:
      - KunstmaanCookieBundle
      - YourOwnBundle
```

## Add visitor type to google analytics

We added the possibility to push a datalayer to google analytics with the type of visitor viewing your website. A new Config entity has been added 
where you can add the ip-addresses of clients or internal. Those ip's will be checked on request and a datalayer will be pushed.

If you want to use this functionality, please read the documentation for the Config Bundle to setup. When you did this, add the following snippet to your config.yml.

```
kunstmaan_config:
    entities:
        - Kunstmaan\CookieBundle\Entity\CookieConfig
```

Following input field should be added to your layout.html.twig. You can add in beneath the kuma-cookie-bar element.

```
<input type="hidden" value="{{ get_visitor_type(app.request) }}" id="kmcc-visitor-type"/>
```

## Increase version number when adding cookies/types or changing content that requires new cookie consent.

In the cookie configuration, there is a version number available. This version number can be increased in the CMS.

### Contributing

We love contributions!
If you're submitting a pull request, please follow the guidelines in the [Submitting pull requests](docs/pull-requests.md)
