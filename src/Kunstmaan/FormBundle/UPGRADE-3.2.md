Upgrade Instructions
====================

## To v3.2 with form submission field order support

To upgrade from a previous version, you have to copy the Doctrine migrations file from this bundle (Resources/DoctrineMigrations/Version20150527162434.php)
to your app/DoctrineMigrations/ folder and run it: ```bin/console doctrine:migrations:migrate```

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
