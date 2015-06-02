# UPGRADE FROM 3.1 to 3.2

## Manual upgrades needed to refactor Sitemaps bundle

### Sitemaps split into languages
This feature generates one central sitemap.xml file at your document root to define a sub sitemap for each language available.
You can now have up to 50000 urls for each language you define.
As a bonus, you only have to register the sitemap index (/sitemap.xml) at the search engines to register all available languages.

#### HOW
In order to upgrade, you need a new route in your app/config/routes.yml.
```yml
KunstmaanSitemapBundle_sitemapIndex:
    resource: "@KunstmaanSitemapBundle/Controller/SitemapController.php"
    type:     annotation
```

### Renamed HiddenFromSitemap
Renamed HiddenFromSitemap to HiddenFromSitemapInterface to comply to the Symfony coding standards.

#### HOW
Make sure you update the interface names and namespaces if you used them in your project.

## Form submission field order support

To upgrade from a previous version, you have to copy the Doctrine migrations file from this bundle (Resources/DoctrineMigrations/Version20150527162434.php)
to your app/DoctrineMigrations/ folder and run it: ```app/console doctrine:migrations:migrate```

This migration will create a new column in the `kuma_form_submission_fields` table.

In case you made any custom FormSubmissionFieldPageParts you'll need to adapt them to fit the new requirements of the FormAdaptorInterface.

    /**
     * Modify the given FormBuilderInterface
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     * @param int                  $sequence    The sequence of the form field
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields, $sequence);

The new sequence field will be used to order the form submission fields.
To take advantage of the new $sequence variable, pass it on to the FormSubmissionField.

    # src/Kunstmaan/FormBundle/Entity/PageParts/SingleLineTextPagePart.php
    /**
     * Modify the form with the fields of the current page part
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     * @param int                  $sequence    The sequence of the form field
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields, $sequence)
    {
        $sfsf = new StringFormSubmissionField();
        $sfsf->setSequence($sequence);
        
        ...
    }
    
The FormHandler will pass the sequence to the adaptForm method of the FormSubmissionFieldPagePart. If you made a custom one, make sure to adapt it.

    # src/Kunstmaan/FormBundle/Helper/FormHandler.php
    /**
     * @param FormPageInterface $page    The form page
     * @param Request           $request The request
     * @param RenderContext     $context The render context
     *
     * @return RedirectResponse|void|null
     */
    public function handleForm(FormPageInterface $page, Request $request, RenderContext $context)
    {
        ...
        $pageParts = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page, $page->getFormElementsContext());
        foreach ($pageParts as $sequence => $pagePart) {
            if ($pagePart instanceof FormAdaptorInterface) {
                $pagePart->adaptForm($formBuilder, $fields, $sequence);
            }
        }
        ...
    }

