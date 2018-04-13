# UPGRADE FROM 4.* to 5.0

## Symfony

The minimum symfony version is set to 3.4.

## Elastica

The ruflin/elastica bundle has been upgraded to the latest 5.1 version.
This version is compatible with the latest elasticsearch version.

The only change that should be made is when you override the NodeSearcher that comes by default from the bundles:

**The setMinimumNumberShouldMatch function is now replaced by the setMinimumShouldMatch function**

When you have created some extra extensions on elastica you should read the changelog:

https://github.com/ruflin/Elastica/blob/master/CHANGELOG.md

## [AdminBundle] Possibility to exclude certain stuff from the exception list
You can use few regex to exclude certain stuff from the exception list.

```yaml
kunstmaan_admin:
    admin_exception_excludes:
        - '/^\.|\.jpg$|\.gif$|.png$/i'
```

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

## Forms must be referenced via FQCN [brakes BC]

All forms were upgraded to be used by **fully qualified class name** instead of creating new form instance.

See more in [Symfony upgrade v2->v3 doc](https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#form)

Before:
```
public function getDefaultAdminType()
{
    return new MyPageAdminType();
}
```

After:
```
public function getDefaultAdminType()
{
    return MyPageAdminType::class;
}
```

A tip: use regex to search part of new form type instances creations (`new .+Type\(`).
"

## VotingBundle

VotingBundle is refactored. If using custom voters implement the new abstract classes
* VoteListener is renamed to AbstractVoteListener
* VotingHelper is renamed to AbstractVotingHelper

## NodeBundle

The nodetranslationlistener has been cleaned for a better flush event. The postFlush event has been removed and everything has been moved to the onFlush event.

## FormBundle

The Discriminator map has been removed from the abstract entity `Kunstmaan\FormBundle\Entity\FormSubmissionField`. This makes it a whole lot easier to
extend the FormSubmission with your own FormSubmission fields. So what happens. If you are using the standard formsubmission, basically nothing. Everything just keeps working.

Now if you have customized the standard formsubmission you probably will have to do some changes. Most vital part is that the `discriminator` value will change from for instance "string" to "stringformsubmissionfield".
As you can see it now uses the entity class name to dynamically map the inheritance. This is where stuff will start to break during querying the database and no result will be found. So for this you will have to manually generate the following migration.

```PHP
    ...
    $this->addSql('UPDATE kuma_form_submission_fields SET discr = "stringformsubmissionfield" WHERE discr = "string"');
    $this->addSql('UPDATE kuma_form_submission_fields SET discr = "textformsubmissionfield" WHERE discr = "text"');
    $this->addSql('UPDATE kuma_form_submission_fields SET discr = "booleanformsubmissionfield" WHERE discr = "boolean"');
    $this->addSql('UPDATE kuma_form_submission_fields SET discr = "choiceformsubmissionfield" WHERE discr = "choice"');
    $this->addSql('UPDATE kuma_form_submission_fields SET discr = "fileformsubmissionfield" WHERE discr = "file"');
    $this->addSql('UPDATE kuma_form_submission_fields SET discr = "emailformsubmissionfield" WHERE discr = "email"');
    ...
```
