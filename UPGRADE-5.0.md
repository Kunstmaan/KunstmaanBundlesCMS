# UPGRADE FROM 4.* to 5.0

## Autowire and refactoring controllers

We have refactored controllers to extend the Symfony 'AbstractController' instead of 'Controller' and we have enabled autowire for the controllers.
This change enables us to start making use of the autowiring function. When extending the controller, you should also make use of autowire or inject at least the container.
With this change we have taken a first step to remove at least at some points the container.

## Elastica

The ruflin/elastica bundle has been upgraded to the latest 5.1 version.
This version is compatible with the latest elasticsearch version.

The only change that should be made is when you override the NodeSearcher that comes by default from the bundles:

**The setMinimumNumberShouldMatch function is now replaced by the setMinimumShouldMatch function**

When you have created some extra extensions on elastica you should read the changelog:

https://github.com/ruflin/Elastica/blob/master/CHANGELOG.md


## [SearchBundle]
BC break: SearchConfigurationInterface contains a new method getLanguagesNotAnalyzed
This method already exists in the NodePagesConfiguration. If you have created your own class that implements SearchConfigurationInterface, you will need to add the method.

```php

    /**
     * @return array
     */
    public function getLanguagesNotAnalyzed()
    {
        $notAnalyzed = [];
        foreach ($this->locales as $locale) {
            if (preg_match('/[a-z]{2}_?+[a-zA-Z]{2}/', $locale)) {
                $locale = strtolower($locale);
            }

            if ( false === array_key_exists($locale, $this->analyzerLanguages) ) {
                $notAnalyzed[] = $locale;
            }
        }

        return $notAnalyzed;
    }
```

## [PagePartBundle] Add pagepart to page view changed
New ui implemented in the backend to add pageparts to a
page. This new UI is opt-in because we do not want to force older projects to
have to do the extra work required to make this new ui look good.
It is possible to configure your pageparts to display a preview image of how
the pagepart will look when added to the page. You can find more information
about enabling and configuring this new view in the README of the
PagePartBundle.

## New datacollectors for symfony toolbar and custom toolbar##

You can find more detailed information on how to use this [here](https://github.com/Kunstmaan/KunstmaanAdminBundle/blob/master/Resources/doc/DataCollectors.md).

## [AdminListBundle] ExportService refactored and new option ods added
BC breaks: Since new option has been added to excelservice (ods) it is necessary to fix all adminlistcontrollers that have the old format requirement regex. ods is not in that list yet.

old:
```php
    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv|xlsx"}, name="{{ bundle_name|lower }}_admin_bike_export")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
	return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
```

new:
```php
    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv|xlsx|ods"}, name="{{ bundle_name|lower }}_admin_bike_export")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
	return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
```

## [LiipImagine]
We upgraded the version of LiipImagine bundle. What this means is that when you're using symlinks for your deployments that those are not supported anymore.
It only works on absolute paths now. To make this work you neet to add the following into you `config_prod.yml` or `config.yml` if you want it for all environments.

```yaml
liip_imagine:
    loaders:
        default:
            filesystem:
                data_root:
                    - "%kernel.root_dir%/../link/to/your/symlinked/path/web"
                    - "%kernel.root_dir%/../web"
```
## Deprecated Form Types
All existing form types have been deprecated in order to satisfy Sensio Insight requirements that form type classes go in a `Form/Type` folder instead of justr `Form`. Existing projects will not break, the old classes are still there, but the logic has been moved into the new classes in the correct folder, and the old classes now extend the new one, and have a `@deprecated` tag.
#### Refactoring your form types
It is very simple, just add `Type` into your use statement.
```php
use Kunstmaan\NodeBundle\Form\ControllerActionAdminType;
```
becomes
```php
use Kunstmaan\NodeBundle\Form\Type\ControllerActionAdminType;
```
