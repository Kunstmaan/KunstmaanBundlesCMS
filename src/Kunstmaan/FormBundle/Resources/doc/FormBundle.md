# FormBundle

## Form pages

Form pages is an extension of the regular pages with page parts ([KunstmaanNodeBundle](https://github.com/Kunstmaan/KunstmaanNodeBundle), [KunstmaanPagePartBundle](https://github.com/Kunstmaan/KunstmaanPagePartBundle)). With the form pages it is possible to create a frontend form which exists out of a mix of form page parts and regular page parts. When the form is submitted a thank you message will be shown, an administrative mail will be sent and a formsubmission will be created.

There are two possibilities to create form pages, you can implement the `Kunstmaan\FormBundle\Helper\FormPageInterface` or you can extend the `Kunstmaan\FormBundle\Entity\AbstractFormPage` which has most of the functionality already implemented. To handle the form creation and the submission of the form you can use the `kunstmaan_form.form_handler` service.

The `kunstmaan_form.form_handler` uses the `kunstmaan_form.form_mailer` to sent the administrative mails. It is possible to create your own mailer for this by implementing `Kunstmaan\FormBundle\Helper\FormMailerInterface` and configuring your new class in the config file `kunstmaan_form.form_mailer.class`. It is also possible to configure your own FormHandler service by implementing `Kunstmaan\FormBundle\Helper\FormHandlerInterface` and configuring the class in the config file `kunstmaan_form.form_handler.class`.

## Form page parts

Form page parts are an extension on the regular page parts you can find in the [KunstmaanPagePartBundle](https://github.com/Kunstmaan/KunstmaanPagePartBundle). With these page parts it is possible to configure a form in the frontend. Form page parts can only be used in collaboration with form pages.

### Default page parts
This bundle provides some default page parts you can use. The following configuration options are available for every form page part:

* **label**: The label used in the frontend form.

#### ChoicePagePart
The choice page part can be used to add single or multiple choice form elements to your form. When you add a choice page part to your form you have the following configuration options:

* **required**: With this you can specify if the user is required to fill in the fields of this form page part.
* **error message required**: The error message that will be shown when the fields are required and nothing is filled in when submitting the form.
* **expanded**: If set to true, radio buttons or checkboxes will be rendered (depending on the multiple value). If false, a select element will be rendered.
* **multiple**: If true, the user will be able to select multiple options (as opposed to choosing just one option). Depending on the value of the expanded option, this will render either a select tag or checkboxes if true and a select tag or radio buttons if false. The returned value will be an array.
* **choices**: The list of possible options the user can choose from. The choices can be entered separated by a new line.
* **placeholder**: This option determines whether or not a special "empty" option (e.g. "Choose an option") will appear at the top of a select widget. This option only applies if both the expanded and multiple options are set to false.

#### FileUploadPagePart
The file upload page part adds the possibility to upload files. When adding a this kind of page part there are a few configuration options available:

* **required**: With this you can specify if the user is required to fill in the fields of this form page part.
* **error message required**: The error message that will be shown when the fields are required and nothing is filled in when submitting the form.

There are also a few customizations you can make to specify where you want to upload the submitted files:

* **form_submission_rootdir**: The full directory where you want to upload the files. Default value: *'%kernel.root_dir%/../web/uploads/formsubmissions'*
* **form_submission_webdir**: The directory starting from your web dir. Default value: *'/uploads/formsubmissions/'*

#### MultiLineTextPagePart
The multi-line text page part adds the possibility to add a text area to you forms. The following configuration options are available when adding this page part type:

* **required**: With this you can specify if the user is required to fill in the fields of this form page part.
* **error message required**: The error message that will be shown when the fields are required and nothing is filled in when submitting the form.
* **regular expression**: If set the entered value will be matched with this regular expression.
* **error message regular expression**: If a regular expression is set and it doesn't match with the given value, this error message will be shown.

#### SingleLineTextPagePart
The single-line text page part can be used to create forms with text input fields. The following configuration options are available when adding this page part type:

* **required**: With this you can specify if the user is required to fill in the fields of this form page part.
* **error message required**: The error message that will be shown when the fields are required and nothing is filled in when submitting the form.
* **regular expression**: If set the entered value will be matched with this regular expression.
* **error message regular expression**: If a regular expression is set and it doesn't match with the given value, this error message will be shown.

#### SubmitButtonPagePart
This page part type adds the possibility to add submit buttons to your forms. Following configuration options are available when adding this page part type to your form:

* **label**: The label used in the frontend form.

### Creating your own page parts
It is possible to create your own page parts by extending from `Kunstmaan\FormBundle\Entity\PageParts\AbstractFormPagePart`. When doing this there are a few methods you need to implement:

```php
public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields);
```
The adaptForm function is used to add the necessary fields to the form used in the frontend. When adding fields to the form there are a few types of fields you can choose from:

* **ChoiceFormSubmissionField**: This can be used to store one or more selected choices to a FormSubmission
* **FileFormSubmissionField**: This can be used to store files to a FormSubmission
* **StringFormSubmissionField**: This can be used to store string values to a FormSubmission
* **TextFormSubmissionField**: This can be used to store multi-line string values (text values) to a FormSubmission

This is also the place to add validation to the form.

```php
public function getDefaultView()
```
This function needs to return the path to the view for this page part, in this view you can access the created form view by using the `frontendform` variable. You can also access the form object by using the `frontendformobject` variable.

```php
public function getDefaultAdminType()
```
This function needs to return a class that extends `Symfony\Component\Form\AbstractType` and is used for the configuration of the page part in the backend.
